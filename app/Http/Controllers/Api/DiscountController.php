<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Discount;
use App\Helpers\Pages;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $discounts = Discount::with(['product'])->withTrashed()
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('product_name', 'like', '%'.$request->keyword.'%');
                            }

                            if ($request->filter != 'all') {
                                if ($request->filter == 'active') {
                                    $where->whereNull('deleted_at');
                                }
                                if ($request->filter == 'inactive') {
                                    $where->whereNotNull('deleted_at');
                                }
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($discounts);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $discounts->total(),
                'per_page' => $discounts->perPage(),
                'current_page' => $discounts->currentPage(),
                'last_page' => $discounts->lastPage(),
                'from' => $discounts->firstItem(),
                'to' => $discounts->lastItem(),
                'pages' => $pages,
                'data' => $discounts->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,_id',
            'valid_thru' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'term' => 'required',
            'total_qty' => 'required'
        ]);
        
        $discount = Discount::firstOrNew(['product_id' => $request->product_id]);
        $discount->product_name = $request->product_name;
        $discount->valid_thru = $request->valid_thru;
        $discount->amount = $request->amount;
        $discount->type = $request->type;
        $discount->term = $request->term;
        $discount->total_qty = $request->total_qty;
        $discount->quota = $request->quota;
        $discount->customer_type = $request->customer_type;
        $discount->customer_type_name = $request->customer_type_name;
        $discount->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }


    public function update($id, Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,_id',
            'valid_thru' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'term' => 'required',
            'total_qty' => 'required'
        ]);
        
        $discount = Discount::find($id);
        $discount->product_name = $request->product_name;
        $discount->valid_thru = $request->valid_thru;
        $discount->amount = $request->amount;
        $discount->type = $request->type;
        $discount->term = $request->term;
        $discount->total_qty = $request->total_qty;
        $discount->quota = $request->quota;
        $discount->customer_type = $request->customer_type;
        $discount->customer_type_name = $request->customer_type_name;
        $discount->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }

    public function show($id)
    {
        $discount = Discount::findOrFail($id);

        return response()->json([
            'type' => 'success',
            'data' => $discount
        ], 200);
    }

    public function toggle($id, Request $request)
    {
        $discount = Discount::withTrashed()->where('_id', $id)->first();

        if ($discount->trashed()) {
            $discount->restore();
        } else {
            $discount->delete();
        }
    }

    public function destroy($id)
    {

        $discount = Discount::withTrashed()->where('_id', $id)->first();
        $discount->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
