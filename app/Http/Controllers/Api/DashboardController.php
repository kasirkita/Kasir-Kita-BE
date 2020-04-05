<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sales;
use Carbon\Carbon;
use Common;
use App\Purchase;
use App\Expense;
use App\SalesDetail;
use App\Product;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $sales = Sales::where('created_at', '>=', Carbon::parse(date('Y-m-d')))
                        ->where('created_at', '<=', Carbon::parse(date('Y-m-d'))->addDay())
                        ->get();
            
        $purchase = Purchase::where('payment_date', '>=', Carbon::parse(date('Y-m-d')))
                        ->where('payment_date', '<=', Carbon::parse(date('Y-m-d')))
                        ->get();
        
        $expense = Expense::where('payment_date', '>=', Carbon::parse(date('Y-m-d')))
                        ->where('payment_date', '<=', Carbon::parse(date('Y-m-d')))
                        ->get();

        $cost = $sales->map(function($item){
            return [
                'cost' => $item->details->sum('cost')
            ];
        });

        $purchase_expense = $purchase->sum('total') + $expense->sum('total');

        return [
            'sales' => Common::formattedNumber($sales->sum('total')),
            'purchase' => Common::formattedNumber($purchase_expense),
            'income' => Common::formattedNumber($sales->sum('total') - $purchase_expense),
            'profit' => Common::formattedNumber(($sales->sum('total') - $cost->sum('cost')) - $purchase_expense)
        ];
    }

    public function getBestSeller()
    {
        $sales = SalesDetail::where('created_at', '>=', Carbon::parse(date('Y-m-d')))
                            ->where('created_at', '<=', Carbon::parse(date('Y-m-d'))->addDay())
                            ->get()
                            ->groupBy('product.name')
                            ->map(function($row){
                                return [
                                    '_id' => $row->first()->product_id,
                                    'price' => $row->first()->price_formatted,
                                    'qty' => $row->sum('qty')
                                ];
                            })
                            ->sortByDesc('qty')
                            ->take(5);
        
        $data = [];
        foreach ($sales as $index => $sale) {
            $data[] = [
                '_id' => $sale['_id'],
                'product_name' => $index,
                'price' => $sale['price'],
                'qty' => $sale['qty']
            ];
        }

        return response()->json([
            'type' => 'success',
            'data' => $data
        ]);
    }

    public function getAlmostOutOfStock()
    {
        $products = Product::whereHas('qty', function($query){
            $query->where('amount', '<=', 5);
        })
        ->orderBy('stock', 'asc')
        ->take(5)
        ->get();

        return response()->json([
            'type' => 'success',
            'data' => $products->load(['qty', 'unit'])
        ]);
    }

    public function getChart()
    {
        $sales = Sales::where('created_at', '>=', Carbon::parse(date('Y-m-d')))
                        ->where('created_at', '<=', Carbon::parse(date('Y-m-d'))->addDay())
                        ->get();
            
        $purchase = Purchase::where('created_at', '>=', Carbon::parse(date('Y-m-d')))
                        ->where('created_at', '<=', Carbon::parse(date('Y-m-d'))->addDay())
                        ->get();

        $label_sales = $sales->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('h:i:s');
        });

        $label_purchase = $purchase->groupBy(function($date){
            return Carbon::parse($date->created_at)->format('h:i:s');
        });

        $merge = $label_sales->keys()->merge($label_purchase->keys());

        $data_sales = [];
        $data_purchase = [];
        foreach ($merge as $index => $merg) {
            $data_sales[$index] = !empty($label_sales[$merg]) ? $label_sales[$merg]->sum('total') : 0;
        }

        foreach ($merge as $index => $merg) {
            $data_purchase[$index] = !empty($label_purchase[$merg]) ? $label_purchase[$merg]->sum('total') : 0;
        }
        
        return response()->json([
            'labels' => $merge,
            'data_sales' => $data_sales,
            'data_purchase' => $data_purchase
        ]);
        

        
    }
}
