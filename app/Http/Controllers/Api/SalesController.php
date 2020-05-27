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
use Carbon\Carbon;
use App\Helpers\Pages;

class SalesController extends Controller
{

    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $sales = Sales::withTrashed()
                        ->with(['customer'])
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('number', 'like', '%'.$request->keyword.'%')
                                    ->orWhereHas('customer', function($query) use ($request){
                                        $query->where('name', 'like', '%'.$request->keyword.'%');
                                    });
                            }

                            if ($request->filter != 'all') {
                                $where->where('status', $request->filter);
                            }

                            if (!empty($request->start_date) && !empty($request->end_date)) {
                                $where->where('created_at', '>=', Carbon::parse($request->start_date))
                                    ->where('created_at', '<=', Carbon::parse($request->end_date)->addDay());
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($sales);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $sales->total(),
                'per_page' => $sales->perPage(),
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'from' => $sales->firstItem(),
                'to' => $sales->lastItem(),
                'pages' => $pages,
                'data' => $sales->all()
            ],
            'selected' => Product::whereNotNull('selected')->count()
        ]);
    }

    public function store(Request $request)
    {
        if (!empty($request->number)) {
            $sales = Sales::firstOrNew(['number' => $request->number]);
        } else {
            $sales = new Sales;
        }
        $sales->number = time().mt_rand(10, 99);
        $sales->customer_id = $request->customer_id;
        $sales->customer_name = $request->customer_name;
        $sales->payment_type = $request->payment_type;
        $sales->user_id = auth()->user()->id;
        $sales->subtotal = $request->subtotal;
        $sales->amount = $request->payment_type == 'cash' ? $request->amount : $request->total;
        $sales->change = $request->payment_type == 'cash' ? $request->change : 0;
        $sales->tax = $request->tax;
        $sales->total_discount = $request->total_discount;
        $sales->total = $request->total;
        $sales->status = $request->status;
        $sales->save();

        if (!empty($request->number)) {
            $sales->details()->forceDelete();
        }

        if (!empty($request->details)) {
            foreach ($request->details as $detail) {

                $unit = collect($request->units)->firstWhere('cart_id', $detail['_id']);

                $fix_price = $request->customer_type == 'wholesaler' ? $detail['wholesale'] : $detail['price'];
                $price = !empty($unit) ? $request->customer_type == 'wholesaler' ? $unit['wholesale'] : $unit['price'] : $fix_price;
                

                $discount = !empty($detail['discount_amount']) ? $detail['type'] == 'percentage' ? $price * ($detail['discount_amount'] / 100) : $detail['discount_amount'] : 0;
                $subtotal = $price * $detail['qty'];
                $sales_detail = new SalesDetail;
                $sales_detail->product_id = $detail['_id'];
                $sales_detail->product_name = $detail['name'];
                $sales_detail->cost = $detail['cost'];
                $sales_detail->price =  $price;
                $sales_detail->qty = $detail['qty'];
                $sales_detail->unit_id = !empty($unit) ? $unit['unit_id'] : $detail['unit_id'];
                $sales_detail->unit_name = !empty($unit) ? $unit['unit_name'] : $detail['unit_name'];
                $sales_detail->subtotal = $subtotal;
                $sales_detail->discount = $discount;
                $sales_detail->total = ($subtotal - $discount);
                $sales->details()->save($sales_detail);

                if ($request->status == 'done') {

                    $qty = !empty($unit) ? (int)($unit['convertion'] * $detail['qty']) : $detail['qty'];

                    $product = Product::find($detail['_id']);
                    $product->decrement('stock', $qty);
                    
    
                    $stock = Stock::where('product_id', $detail['_id'])->first();
                    $stock->decrement('amount', $qty);
    
                    $stock_detail = new StockDetail;
                    $stock_detail->amount = $qty;
                    $stock_detail->description = 'Penjualan '.$sales->number;
                    $stock_detail->type = '-';
                    $stock_detail->user_id = auth()->user()->id;
                    $stock->details()->save($stock_detail);
    
                    $discount = Discount::where('product_id', $detail['_id'])->first();
    
                    if (!empty($discount) && $discount->quota > 0) {
                        $discount->decrement('quota');
                    }
                }

            }
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan',
            'data' => $sales->load('details')
        ]);

    }

    public function show($id)
    {
        $sales = Sales::find($id)->load(['customer', 'user', 'details', 'details.product']);

        return response()->json([
            'type' => 'success',
            'data' => $sales
        ], 200);
    }

    public function destroy($id)
    {
        $sales = Sales::find($id);
        $sales->details()->forceDelete();
        $sales->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

}
