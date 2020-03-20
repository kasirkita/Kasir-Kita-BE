<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Permission;

class PermissionController extends Controller
{
    public function list()
    {
        $permissions = Permission::all();

        return response()->json([
            'type' => 'success',
            'data' => $permissions
        ], 200);
    }
}
