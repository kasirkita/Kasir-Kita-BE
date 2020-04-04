<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Pages;
use App\StockDetail;
use Carbon\Carbon;
use App\Stock;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $stocks = StockDetail::withTrashed()
                        ->with(['user'])
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('description', 'like', '%'.$request->keyword.'%')
                                     ->orWhere('amount', 'like', '%'.$request->keyword.'%');
                            }

                            if (!empty($request->start_date) && !empty($request->end_date)) {
                                $where->where('created_at', '>=', Carbon::parse($request->start_date))
                                    ->where('created_at', '<=', Carbon::parse($request->end_date)->addDay());
                            }

                            
                                
                            $where->whereHas('stock', function($wherehas) use ($request) {
                                $wherehas->where('product_id', $request->product_id);
                            });

                        

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($stocks);
        $stock = Stock::where('product_id', $request->product_id)->first();

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $stocks->total(),
                'per_page' => $stocks->perPage(),
                'current_page' => $stocks->currentPage(),
                'last_page' => $stocks->lastPage(),
                'from' => $stocks->firstItem(),
                'to' => $stocks->lastItem(),
                'pages' => $pages,
                'data' => $stocks->all(),
                'current_stock' => !empty($stock) ? $stock->amount : 0
            ],
        ]);
    }

    public function store(Request $request) {
        
        $stock = Stock::where('product_id', $request->product_id)->first();

        if ($stock->amount > $request->real_stock) {
            $type = '-';
            $amount =  $stock->amount - $request->real_stock;
        } else {
            $type = '+';
            $amount = $request->real_stock - $stock->amount;
        }

        $stock->amount = (float)$request->real_stock;
        $stock->save();

        $stock_details = new StockDetail;
        $stock_details->type = $type;
        $stock_details->amount = (float)$amount;
        $stock_details->description = $request->description;
        $stock_details->user_id = auth()->user()->id;
        $stock->details()->save($stock_details);

        return response()->json([
            'type' => 'success',
            'message' => 'Stock berhasil di sesuaikan'
        ], 200);

    }
}
