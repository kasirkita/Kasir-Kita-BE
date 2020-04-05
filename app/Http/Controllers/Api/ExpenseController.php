<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Expense;
use App\Helpers\Pages;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $expenses = Expense::with(['in_charge'])
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

        $pages = Pages::generate($expenses);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $expenses->total(),
                'per_page' => $expenses->perPage(),
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'from' => $expenses->firstItem(),
                'to' => $expenses->lastItem(),
                'pages' => $pages,
                'data' => $expenses->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'payment_date' => 'required'
        ]);

        $expense = new Expense;
        $expense->number = $request->number;
        $expense->payment_date = $request->payment_date;
        $expense->product_name = $request->product_name;
        $expense->price = (float)$request->price;
        $expense->qty = (float)$request->qty;
        $expense->total = (float) $request->price * $request->qty;
        $expense->supplier_name = $request->supplier_name;
        $expense->in_charge_id = $request->in_charge_id;
        $expense->in_charge_name = $request->in_charge_name;
        $expense->user_id = auth()->user()->id;
        $expense->notes = $request->notes;

        if (!empty($request->file('evidence'))) {

            $file = $request->file('evidence');
            $file_extension = $file->getClientOriginalExtension();
            $filename = rand(0, 99).time().'.'.$file_extension;
            $file->storeAs('public/documents', $filename);
            $expense->evidence = $filename;

        }

        $expense->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }

    public function update($id, Request $request)
    {
        $request->validate([
            'number' => 'required',
            'payment_date' => 'required'
        ]);

        $expense = Expense::find($id);
        $expense->number = $request->number;
        $expense->payment_date = $request->payment_date;
        $expense->product_name = $request->product_name;
        $expense->price = (float)$request->price;
        $expense->qty = $request->qty;
        $expense->total = (float) $request->price * $request->qty;
        $expense->supplier_name = $request->supplier_name;
        $expense->in_charge_id = $request->in_charge_id;
        $expense->in_charge_name = $request->in_charge_name;
        $expense->user_id = auth()->user()->id;
        $expense->notes = $request->notes;

        if (!empty($request->file('evidence'))) {

            if (!empty($expense->evidence)) {
                if (Storage::disk('documents')->exists($expense->evidence)) {
                    Storage::disk('documents')->delete($expense->evidence);
                }
            }

            $file = $request->file('evidence');
            $file_extension = $file->getClientOriginalExtension();
            $filename = rand(0, 99).time().'.'.$file_extension;
            $file->storeAs('public/documents', $filename);
            $expense->evidence = $filename;

        }

        $expense->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }

    public function show($id)
    {
        $expense = Expense::findOrFail($id);

        return response()->json([
            'type' => 'success',
            'data' => $expense->load(['in_charge', 'user'])
        ], 200);
    }

    public function destroy($id)
    {

        $expense = Expense::withTrashed()->where('_id', $id)->first();
        
        if (!empty($expense->evidence)) {
            if (Storage::disk('documents')->exists($expense->evidence)) {
                Storage::disk('documents')->delete($expense->evidence);
            }
        }

        $expense->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
