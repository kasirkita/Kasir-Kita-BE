<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Supplier;
use App\Helpers\Pages;

class SupplierController extends Controller
{
    public function list(Request $request)
    {
        $suppliers = Supplier::when(!empty($request->keyword), function($query) use ($request){
            $query->where('name', 'like', '%'.$request->keyword.'%');
        })
        ->take(10)
        ->get();

        return response()->json([
            'type' => 'success',
            'data' => $suppliers
        ], 200);
    }

    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $suppliers = Supplier::withTrashed()
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('name', 'like', '%'.$request->keyword.'%')
                                ->orWhere('email', 'like', '%'.$request->keyword.'%');
                            }

                            if ($request->filter != 'all') {
                                if ($request->filter == 'active') {
                                    $where->whereNull('deleted_at');
                                }
                                if ($request->filter == 'inactive') {
                                    $where->whereNotNull('deleted_at');
                                }
                            }

                        })
                        ->orderBy($ordering->type, $ordering->sort)
                        ->paginate((int)$request->perpage);

        $pages = Pages::generate($suppliers);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $suppliers->total(),
                'per_page' => $suppliers->perPage(),
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'from' => $suppliers->firstItem(),
                'to' => $suppliers->lastItem(),
                'pages' => $pages,
                'data' => $suppliers->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone_number = $request->phone_number;
        $supplier->address = $request->address;
        $supplier->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }


    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $supplier = Supplier::find($id);
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone_number = $request->phone_number;
        $supplier->address = $request->address;
        $supplier->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);

        return response()->json([
            'type' => 'success',
            'data' => $supplier
        ], 200);
    }

    public function toggle($id, Request $request)
    {
        $supplier = Supplier::withTrashed()->where('_id', $id)->first();

        if ($supplier->trashed()) {
            $supplier->restore();
        } else {
            $supplier->delete();
        }
    }

    public function destroy($id)
    {

        $supplier = Supplier::withTrashed()->where('_id', $id)->first();
        $supplier->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
