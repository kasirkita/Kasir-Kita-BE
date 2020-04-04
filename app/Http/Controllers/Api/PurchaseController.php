<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Purchase;
use App\PurchaseDetail;
use App\Helpers\Pages;
use Carbon\Carbon;
use App\Product;
use App\Stock;
use App\StockDetail;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $purchases = Purchase::with(['supplier', 'in_charge'])
                        ->withTrashed()
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('number', 'like', '%'.$request->keyword.'%');
                            }

                            if (!empty($request->payment_date_start) && !empty($request->payment_date_end)) {
                                $where->where('payment_date', '>=', Carbon::parse($request->payment_date_start))
                                    ->where('payment_date', '<=', Carbon::parse($request->payment_date_end));
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($purchases);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $purchases->total(),
                'per_page' => $purchases->perPage(),
                'current_page' => $purchases->currentPage(),
                'last_page' => $purchases->lastPage(),
                'from' => $purchases->firstItem(),
                'to' => $purchases->lastItem(),
                'pages' => $pages,
                'data' => $purchases->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'payment_date' => 'required'
        ]);

        $purchase = new Purchase;
        $purchase->number = $request->number;
        $purchase->payment_date = $request->payment_date;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->supplier_name = $request->supplier_name;
        $purchase->in_charge_id = $request->in_charge_id;
        $purchase->in_charge_name = $request->in_charge_name;
        $purchase->user_id = auth()->user()->id;
        $purchase->notes = $request->notes;
        $purchase->subtotal = (float)$request->subtotal;
        $purchase->tax = (float)$request->tax;
        $purchase->total_discount = (float)$request->total_discount;
        $purchase->total = (float)$request->total;

        if (!empty($request->file('evidence'))) {

            $file = $request->file('evidence');
            $file_extension = $file->getClientOriginalExtension();
            $filename = rand(0, 99).time().'.'.$file_extension;
            $file->storeAs('public/documents', $filename);
            $purchase->evidence = $filename;

        }

        $purchase->save();

        if (!empty($request->details)) {
            foreach (json_decode($request->details) as $detail) {
                $purchase_detail = new PurchaseDetail;
                $purchase_detail->product_id = $detail->_id;
                $purchase_detail->product_name = $detail->name;
                $purchase_detail->cost = $detail->cost;
                $purchase_detail->price = $detail->price;
                $purchase_detail->wholesale = $detail->wholesale;
                $purchase_detail->qty = $detail->qty;
                $purchase_detail->subtotal = $detail->cost * $detail->qty;
                $purchase->details()->save($purchase_detail);
    
                $product = Product::find($detail->_id);
                $product->price = $detail->price;
                $product->wholesale = $detail->wholesale;
                $product->cost = $detail->cost;
                $product->save();
    
                $stock = Stock::where('product_id', $detail->_id)->first();
                $stock->increment('amount', $detail->qty);
    
                $stock_detail = new StockDetail;
                $stock_detail->amount = $detail->qty;
                $stock_detail->description = 'Pembelian '.$purchase->number;
                $stock_detail->type = '+';
                $stock->details()->save($stock_detail);
            }

        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }

    public function show($id)
    {
        $purchase = Purchase::findOrFail($id);

        return response()->json([
            'type' => 'success',
            'data' => $purchase->load(['details', 'in_charge', 'user', 'supplier'])
        ], 200);
    }

    public function destroy($id)
    {

        $purchase = Purchase::withTrashed()->where('_id', $id)->first();
        
        if (!empty($purchase->evidence)) {
            if (Storage::disk('documents')->exists($purchase->evidence)) {
                Storage::disk('documents')->delete($purchase->evidence);
            }
        }

        $purchase->details()->forceDelete();
        $purchase->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
