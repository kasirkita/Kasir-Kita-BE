<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sales;
use App\SalesDetail;
use App\Stock;
use App\StockDetail;
use App\Product;
use App\Discount;

class SalesController extends Controller
{
    public function store(Request $request)
    {
        $sales = new Sales;
        $sales->number = time().mt_rand(10, 99);
        $sales->customer_id = $request->customer_id;
        $sales->customer_name = $request->customer_name;
        $sales->payment_type = $request->payment_type;
        $sales->user_id = auth()->user()->id;
        $sales->subtotal = $request->subtotal;
        $sales->amount = $request->amount;
        $sales->change = $request->change;
        $sales->tax = $request->tax;
        $sales->total_discount = $request->total_discount;
        $sales->total = $request->total;
        $sales->status = $request->status;
        $sales->save();

        if (!empty($request->details)) {
            foreach ($request->details as $detail) {

                $sales_detail = new SalesDetail;
                $sales_detail->item_id = $detail['_id'];
                $sales_detail->item_name = $detail['name'];
                $sales_detail->price = $request->customer_type === 'wholesaler' ? $detail['wholesale'] : $detail['price'];
                $sales_detail->qty = $detail['qty'];
                $sales_detail->subtotal = $request->customer_type === 'wholesaler' ? $detail['wholesale'] * $detail['qty'] : $detail['price'] * $detail['qty'];
                $sales_detail->discount = !empty($detail['discount_amount']) ? $detail['type'] == 'percentage' ? $detail['price'] * ($detail['discount_amount'] / 100) : $detail['discount_amount'] : 0;
                $sales->details()->save($sales_detail);

                $product = Product::find($detail['_id']);
                $product->decrement('stock', $detail['qty']);

                $stock = Stock::where('product_id', $detail['_id'])->first();
                $stock->decrement('amount', $detail['qty']);

                $stock_detail = new StockDetail;
                $stock_detail->amount = $detail['qty'];
                $stock_detail->description = 'Penjualan '.$sales->number;
                $stock_detail->type = '-';
                $stock->details()->save($stock_detail);

                $discount = Discount::where('product_id', $detail['_id'])->first();

                if (!empty($discount) && $discount->quota > 0) {
                    $discount->decrement('quota');
                }
            }
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $sales->load('details')
        ]);

    }
}
