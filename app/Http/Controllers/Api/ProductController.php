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
use App\Exports\ProductExport;
use PDF;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $products = Product::withTrashed()
                        ->with(['category', 'qty', 'unit'])
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

                            if ($request->filter != 'all') {
                                if ($request->filter == 'active') {
                                    $where->whereNull('deleted_at');
                                }
                                if ($request->filter == 'inactive') {
                                    $where->whereNotNull('deleted_at');
                                }
                                if ($request->filter == 'selected') {
                                    $where->whereNotNull('selected');
                                }
                                if ($request->filter == 'unselected') {
                                    $where->whereNull('selected');
                                }
                            }

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
            ],
            'selected' => Product::whereNotNull('selected')->count()
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
        $product->category_id = !empty($category) ? $category->id : null;
        $product->unit = $request->unit_label;
        $product->unit_id = !empty($unit) ? $unit->id : null;
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
        $product->category_id = !empty($category) ? $category->id : null;
        $product->unit = $request->unit_label;
        $product->unit_id = !empty($unit) ? $unit->id : null;
        $product->stock = (float)$request->stock;
        $product->save();


        $stock = new Stock;
        $stock->amount = (float)$request->stock;
        $product->qty()->save($stock);

        $stock_detail = new StockDetail;
        $stock_detail->amount = (float)$request->stock;
        $stock_detail->description = 'Stok awal';
        $stock_detail->type = '+';
        $stock_detail->user_id = auth()->user()->id;
        $stock->details()->save($stock_detail);

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }

    public function show($id)
    {
        $product = Product::findOrFail($id)->load(['category', 'qty', 'unit']);

        return response()->json([
            'type' => 'success',
            'data' => $product
        ], 200);
    }

    public function toggle($id, Request $request)
    {
        $product = Product::withTrashed()->where('_id', $id)->first();

        if ($product->trashed()) {
            $product->restore();
        } else {
            $product->delete();
        }
    }

    public function destroy($id)
    {

        $product = Product::withTrashed()->where('_id', $id)->first();
        $product->stock->details()->forceDelete();
        $product->qty()->forceDelete();
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
            Excel::import(new ProductImport, $file, \Maatwebsite\Excel\Excel::CSV);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diimport!'
        ], 200);
    }

    public function select($id)
    {
        $product = Product::withTrashed()->where('_id', $id)->first();

        if (!empty($product->selected)) {
            $product->selected = null;
        } else {
            $product->selected = true;
        }

        $product->save();

        return response()->json([
            'type' => 'success',
            'selected' => Product::whereNotNull('selected')->count()
        ], 201);

    }

    public function template()
    {
        return Excel::download(new ProductExport, 'product_template_import.csv',\Maatwebsite\Excel\Excel::CSV);
    }

    public function print()
    {
        $data = Product::whereNotNull('selected')->get();
        $pdf = PDF::loadView('pdf.label', ['data' => $data]);
        return $pdf->download('label.pdf');
        // return view('pdf.label', ['data' => $data]);
    }

    public function printThermal()
    {
        $data = Product::whereNotNull('selected')->get();
        return response()->json([
            'type' => 'success',
            'data' => $data], 200);
    }

    public function list(Request $request)
    {
        $products = Product::when(!empty($request->keyword), function($query) use ($request){
            $query->where('name', 'like', '%'.$request->keyword.'%')
                    ->orWhere('code', 'like', '%'.$request->keyword.'%');
        })
        ->doesntHave('discount')
        ->orderBy('name')
        ->take(10)
        ->get();

        return response()->json([
            'type' => 'success',
            'data' => $products
        ], 200);
    }
}
