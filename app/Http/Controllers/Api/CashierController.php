<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class CashierController extends Controller
{
    public function cart($code)
    {
        $product = Product::where('code', $code)->first();

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
