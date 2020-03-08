<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Unit;
use App\Helpers\Pages;
use App\Helpers\Common;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function list(Request $request)
    {
        $units = Unit::when(!empty($request->keyword), function($query) use ($request){
            $query->where('name', 'like', '%'.$request->keyword.'%');
        })
        ->take(10)
        ->get();

        return response()->json([
            'type' => 'success',
            'data' => $units
        ], 200);
    }

    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $categories = Unit::withTrashed()
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('name', 'like', '%'.$request->keyword.'%');
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

        $pages = Pages::generate($categories);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
                'pages' => $pages,
                'data' => $categories->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $unit = Unit::firstOrNew([
            'slug' => Str::slug($request->name)
        ]);
        $unit->name = $request->name;
        $unit->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }


    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $unit = Unit::find($id);
        $unit->name = $request->name;
        $unit->slug = Common::createSlug($request->name, 'unit', $id);
        $unit->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);

        return response()->json([
            'type' => 'success',
            'data' => $unit
        ], 200);
    }

    public function toggle($id, Request $request)
    {
        $unit = Unit::withTrashed()->where('_id', $id)->first();

        if ($unit->trashed()) {
            $unit->restore();
        } else {
            $unit->delete();
        }
    }

    public function destroy($id)
    {

        $unit = Unit::withTrashed()->where('_id', $id)->first();
        $unit->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
