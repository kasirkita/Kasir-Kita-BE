<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Helpers\Pages;
use Illuminate\Support\Str;
use App\Category;
use App\Unit;
use App\Stock;
use App\StockDetail;
use Excel;
use App\Imports\ProductImport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $products = Product::withTrashed()
                            ->with(['category', 'stock', 'unit'])
                            ->where(function($where) use ($request){

                                if (!empty($request->keyword)) {
                                    $where->where('name', 'like', '%'.$request->keyword.'%')
                                        ->orWhere('code', 'like', '%'.$request->keyword.'%')
                                        ->orWhere('description', 'like', '%'.$request->keyword.'%')
                                        ->orWhere('price', 'like', '%'.$request->keyword.'%')
                                        ->orWhere('cost', 'like', '%'.$request->keyword.'%')
                                        ->orWhere('wholesale', 'like', '%'.$request->keyword.'%')
                                        ->orWhere('picture', 'like', '%'.$request->keyword.'%')
                                        ->orWhereHas('category', function($whereHas) use ($request){
                                            $whereHas->where('name', 'like', $request->keyword);
                                        });
                                }

                                // if ($request->filter != 'all') {
                                    // if ($request->filter == 'active') {
                                        $where->whereNull('deleted_at');
                                //     }
                                //     if ($request->filter == 'inactive') {
                                //         $where->whereNotNull('deleted_at');
                                //     }
                                //     if ($request->filter == 'selected') {
                                //         $where->whereNull('selected');
                                //     }
                                //     if ($request->filter == 'unselected') {
                                //         $where->whereNotNull('selected');
                                //     }
                                // }

                            })
                            ->orderBy($ordering->type, $ordering->sort)
                            ->paginate((int)$request->perpage);

        $pages = Pages::generate($products);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'pages' => $pages,
                'data' => $products->all()
            ]
        ]);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'code' => 'required|unique:product,code,'.$id.',_id',
            'name' => 'required',
            'cost' => 'required',
            'price' => 'required',
            'wholesale' => 'required',
            'stock' => 'required'
        ]);

        if (!empty($request->category_id)) {
            $category = Category::firstOrNew([
                'slug' => Str::slug($request->category_id)
            ]);
            $category->name = $request->category_id;
            $category->save();
        }


        if (!empty($request->unit_id)) {
            $unit = Unit::firstOrNew([
                'slug' => Str::slug($request->unit_id)
            ]);
            $unit->name = $request->unit_id;
            $unit->save();
        }

        $product = Product::findOrFail($id);
        $product->code = $request->code;
        $product->name = $request->name;
        $product->cost = $request->cost;
        $product->price = $request->price;
        $product->wholesale = $request->wholesale;
        $product->category = $request->category_label;
        $product->category_id = $category->id;
        $product->unit = $request->unit_label;
        $product->unit_id = $unit->id;
        $product->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:product',
            'name' => 'required',
            'cost' => 'required',
            'price' => 'required',
            'wholesale' => 'required',
            'stock' => 'required'
        ]);

        if (!empty($request->category_id)) {
            $category = Category::firstOrNew([
                'slug' => Str::slug($request->category_id)
            ]);
            $category->name = $request->category_id;
            $category->save();
        }


        if (!empty($request->unit_id)) {
            $unit = Unit::firstOrNew([
                'slug' => Str::slug($request->unit_id)
            ]);
            $unit->name = $request->unit_id;
            $unit->save();
        }

        $product = new Product;
        $product->code = $request->code;
        $product->name = $request->name;
        $product->cost = $request->cost;
        $product->price = $request->price;
        $product->wholesale = $request->wholesale;
        $product->category = $request->category_label;
        $product->category_id = $category->id;
        $product->unit = $request->unit_label;
        $product->unit_id = $unit->id;
        $product->save();


        $stock = new Stock;
        $stock->amount = $request->stock;
        $product->stock()->save($stock);

        $stock_detail = new StockDetail;
        $stock_detail->amount = $request->stock;
        $stock_detail->description = 'Stok awal';
        $stock_detail->type = '+';
        $stock->details()->save($stock_detail);

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }

    public function show($id)
    {
        $product = Product::findOrFail($id)->load(['category', 'stock', 'unit']);

        return response()->json([
            'type' => 'success',
            'data' => $product
        ], 200);
    }

    public function toggle($id, Request $request)
    {

        
        if ($request->active) {

            $product = Product::onlyTrashed()->firstOrFail(['id' => $id]);
            $product->restore();

            return response()->json([
                'type' => 'success',
                'message' => 'Data berhasil diaktifkan'
            ], 201);

        } else {

            $product = Product::firstOrFail(['id' => $id]);
            $product->delete();

            return response()->json([
                'type' => 'success',
                'message' => 'Data berhasil dinonaktifkan'
            ], 201);

        }
    }

    public function destroy($id)
    {

        $product = Product::withTrashed()->firstOrFail(['id', $id]);
        $product->stock->details()->forceDelete();
        $product->stock()->forceDelete();
        $product->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }

    public function import(Request $request)
    {
        if (!empty($request->file)) {

            $file = $request->file('file');
            Excel::import(new ProductImport, $file);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diimport!'
        ], 200);
    }
}
