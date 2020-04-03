<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Discount;
use App\Helpers\Pages;
use App\Helpers\Common;
use PDF;

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
                                if ($request->filter == 'selected') {
                                    $where->whereNotNull('selected');
                                }
                                if ($request->filter == 'unselected') {
                                    $where->whereNull('selected');
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
            ],
            'selected' => Discount::whereNotNull('selected')->count()
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

    public function select($id)
    {
        $discount = Discount::withTrashed()->where('_id', $id)->first();

        if (!empty($discount->selected)) {
            $discount->selected = null;
        } else {
            $discount->selected = true;
        }

        $discount->save();

        return response()->json([
            'type' => 'success',
            'selected' => Discount::whereNotNull('selected')->count()
        ], 201);

    }

    public function print()
    {
        $data = Discount::whereNotNull('selected')->get();
        $pdf = PDF::loadView('pdf.label_discount', ['data' => $data->load('product')]);
        return $pdf->download('label_discount.pdf');
        // return view('pdf.label', ['data' => $data]);
    }

    public function printThermal()
    {
        $data = Discount::with(['product'])->whereNotNull('selected')->get();
        $new_data = $data->map(function($item) {
            $term =  $item->term == '>' ? 'lebih dari ' : '';
            $unit =  $item->product->unit ? $item->product->unit->name : '';

            return [
                'name' => $item->product->name,
                'tnc' => 'Setiap pembelian ' .  $term . $item->total_qty . $unit,
                'valid_thru_formatted' => $item->valid_thru_formatted,
                'price' => $item->product->price_formatted,
                'customer_type' => $item->customer_type,
                'discount' => Common::formattedNumber($item->type == 'fix' ? $item->product->price - $item->amount : $item->product->price  - ($item->product->price * ($item->amount / 100))),
            ];
        });

        return response()->json([
            'type' => 'success',
            'data' => $new_data], 200);
    }
}
