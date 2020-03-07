<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Unit;

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
}
