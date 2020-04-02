<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Discount;
use Carbon\Carbon;

class CashierController extends Controller
{
    public function cart($code)
    {
        $product = Product::where('code', $code)->first();

        if (!empty($product->discount)) {

            if ($product->discount->valid_thru->greaterThanOrEqualTo(Carbon::now())) {
                if (is_null($product->discount->quota) || $product->discount->quota > 0 ) {
                    $product->discount_amount = $product->discount->amount;
                    $product->type = $product->discount->type;
                    $product->total_qty = $product->discount->total_qty;
                    $product->term = $product->discount->term;
                    $product->customer_type = $product->discount->customer_type;
                }
            }

        }

        if (!empty($product)) {
            
            return response()->json([
                'type' => 'success',
                'data' => $product
            ], 200);

        } else {

            return response()->json([
                'type' => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 422);

        }
    }

    public function search(Request $request)
    {
        $products = Product::where('name', 'like', '%'.$request->keyword.'%')
                            ->orWhere('code', 'like', '%'.$request->keyword.'%')
                            ->take(5)
                            ->get();

        return response()->json([
            'type' => 'success',
            'data' => $products
        ], 200);
    }
}
