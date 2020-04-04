<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SalesDetail;
use App\PurchaseDetail;
use Carbon\Carbon;
use App\Helpers\Pages;
use Common;
use PDF;
use Excel;
use App\Exports\ReportSalesExport;
use App\Exports\ReportPurchaseExport;
use App\Exports\ReportExpenseExport;
use App\Exports\ReportStockExport;
use App\Expense;
use App\StockDetail;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $sales = SalesDetail::where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('product_name', 'like', '%'.$request->keyword.'%');
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
                'data' => $sales->all(),
                'total_selled' => $sales->sum('qty'),
                'total_sales' => Common::formattedNumber($sales->sum('total')),
                'total_profit' => Common::formattedNumber($sales->sum('total') - $sales->sum('cost'))
            ]
        ]);
    }

    public function printSales($type, Request $request)
    {

        $sales = SalesDetail::where(function($where) use ($request){

            if (!empty($request->keyword)) {
                $where->where('product_name', 'like', '%'.$request->keyword.'%');
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $where->where('created_at', '>=', Carbon::parse($request->start_date))
                    ->where('created_at', '<=', Carbon::parse($request->end_date)->addDay());
            }

        })
        ->orderBy('created_at', 'asc')
        ->orderBy('product_name', 'asc')
        ->get();

        $data = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'sales' => $sales,
            'total_selled' => $sales->sum('qty'),
            'total_sales' => Common::formattedNumber($sales->sum('total')),
            'total_profit' => Common::formattedNumber($sales->sum('total') - $sales->sum('cost'))
        ];

        if ($type == 'pdf') {
            $pdf = PDF::loadView('pdf.report_sales_pdf', $data);
            return $pdf->download('report-sales.pdf');
        } else {
            return Excel::download(new ReportSalesExport($data), 'report sales.xlsx');
        }

    }


    public function purchase(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $purchase = PurchaseDetail::with(['purchase'])->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('product_name', 'like', '%'.$request->keyword.'%');
                            }

                            if (!empty($request->start_date) && !empty($request->end_date)) {
                                $where->whereHas('purchase', function($query) use ($request){
                                    $query->where('payment_date', '>=', Carbon::parse($request->start_date))
                                    ->where('payment_date', '<=', Carbon::parse($request->end_date)->addDay());
                                });
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($purchase);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $purchase->total(),
                'per_page' => $purchase->perPage(),
                'current_page' => $purchase->currentPage(),
                'last_page' => $purchase->lastPage(),
                'from' => $purchase->firstItem(),
                'to' => $purchase->lastItem(),
                'pages' => $pages,
                'data' => $purchase->all(),
                'total_purchased' => $purchase->sum('qty'),
                'total_purchase' => Common::formattedNumber($purchase->sum('subtotal')),
            ]
        ]);
    }

    public function printPurchase($type, Request $request)
    {

        $purchases = PurchaseDetail::where(function($where) use ($request){

            if (!empty($request->keyword)) {
                $where->where('product_name', 'like', '%'.$request->keyword.'%');
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $where->whereHas('purchase', function($query) use ($request){
                    $query->where('payment_date', '>=', Carbon::parse($request->start_date))
                    ->where('payment_date', '<=', Carbon::parse($request->end_date)->addDay());
                });
            }

        })
        ->orderBy('created_at', 'asc')
        ->orderBy('product_name', 'asc')
        ->get();

        $data = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purchases' => $purchases,
            'total_purchased' => $purchases->sum('qty'),
            'total_purchase' => Common::formattedNumber($purchases->sum('subtotal')),
        ];

        if ($type == 'pdf') {
            $pdf = PDF::loadView('pdf.report_purchase_pdf', $data);
            return $pdf->download('report-purchase.pdf');
        } else {
            return Excel::download(new ReportPurchaseExport($data), 'report purchase.xlsx');
        }

    }

    public function expense(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $expense = Expense::where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('product_name', 'like', '%'.$request->keyword.'%');
                            }

                            if (!empty($request->start_date) && !empty($request->end_date)) {
                                $where->where('payment_date', '>=', Carbon::parse($request->start_date))
                                    ->where('payment_date', '<=', Carbon::parse($request->end_date)->addDay());
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($expense);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $expense->total(),
                'per_page' => $expense->perPage(),
                'current_page' => $expense->currentPage(),
                'last_page' => $expense->lastPage(),
                'from' => $expense->firstItem(),
                'to' => $expense->lastItem(),
                'pages' => $pages,
                'data' => $expense->all(),
                'total_expensed' => $expense->sum('qty'),
                'total_expense' => Common::formattedNumber($expense->sum('total')),
            ]
        ]);
    }

    public function printExpense($type, Request $request)
    {

        $expenses = Expense::where(function($where) use ($request){

            if (!empty($request->keyword)) {
                $where->where('product_name', 'like', '%'.$request->keyword.'%');
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $where->where('payment_date', '>=', Carbon::parse($request->start_date))
                    ->where('payment_date', '<=', Carbon::parse($request->end_date)->addDay());
            }

        })
        ->orderBy('payment_date', 'asc')
        ->orderBy('product_name', 'asc')
        ->get();

        $data = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'expenses' => $expenses,
            'total_expensed' => $expenses->sum('qty'),
            'total_expense' => Common::formattedNumber($expenses->sum('subtotal')),
        ];

        if ($type == 'pdf') {
            $pdf = PDF::loadView('pdf.report_expense_pdf', $data);
            return $pdf->download('report-expense.pdf');
        } else {
            return Excel::download(new ReportExpenseExport($data), 'report expense.xlsx');
        }

    }

    public function stock(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $stock = StockDetail::with(['stock.product', 'user'])
                            ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->whereHas('stock.product', function($query) use($request){
                                    $query->where('name', 'like', '%'.$request->keyword.'%');
                                });
                            }

                            if (!empty($request->start_date) && !empty($request->end_date)) {
                                $where->where('created_at', '>=', Carbon::parse($request->start_date))
                                    ->where('created_at', '<=', Carbon::parse($request->end_date)->addDay());
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($stock);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $stock->total(),
                'per_page' => $stock->perPage(),
                'current_page' => $stock->currentPage(),
                'last_page' => $stock->lastPage(),
                'from' => $stock->firstItem(),
                'to' => $stock->lastItem(),
                'pages' => $pages,
                'data' => $stock->all()
            ]
        ]);
    }

    public function printStock($type, Request $request)
    {

        $stocks = StockDetail::with(['stock.product', 'user'])->where(function($where) use ($request){

            if (!empty($request->keyword)) {
                $where->whereHas('stock.product', function($query) use($request){
                    $query->where('name', 'like', '%'.$request->keyword.'%');
                });
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $where->where('created_at', '>=', Carbon::parse($request->start_date))
                    ->where('created_at', '<=', Carbon::parse($request->end_date)->addDay());
            }

        })
        ->orderBy('created_at', 'asc')
        ->orderBy('product_name', 'asc')
        ->get();

        $data = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'stocks' => $stocks,
        ];

        if ($type == 'pdf') {
            $pdf = PDF::loadView('pdf.report_stock_pdf', $data);
            return $pdf->download('report-stock.pdf');
        } else {
            return Excel::download(new ReportStockExport($data), 'report stock.xlsx');
        }

    }


}
