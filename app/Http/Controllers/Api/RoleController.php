<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;
use App\Helpers\Pages;

class RoleController extends Controller
{
    public function list(Request $request)
    {
        $roles = Role::when(!empty($request->keyword), function($query) use ($request){
            $query->where('name', 'like', '%'.$request->keyword.'%');
        })
        ->take(10)
        ->get();

        return response()->json([
            'type' => 'success',
            'data' => $roles
        ], 200);
    }

    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $roles = Role::withTrashed()
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

        $pages = Pages::generate($roles);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $roles->total(),
                'per_page' => $roles->perPage(),
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem(),
                'pages' => $pages,
                'data' => $roles->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $role = new Role;
        $role->name = $request->name;
        $role->save();
        
        foreach ($request->permissions as $index => $permission) {
            $role->permissions()->create([
                'type' => $index,
                'allow' => $permission
            ]);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!',
            'data' => $request->permissions
        ], 201);

    }


    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();

        $role->permissions()->delete();
        foreach ($request->permissions as $index => $permission) {
            $role->permissions()->create([
                'type' => $index,
                'allow' => $permission
            ]);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $permissions = [];
        foreach ($role->permissions as $permission) {
            $permissions[$permission->type] = $permission->allow;
        }

        $role->perms = $permissions;
            return response()->json([
                'type' => 'success',
                'data' => $role
            ], 200);
    }

    public function toggle($id, Request $request)
    {
        $role = Role::withTrashed()->where('_id', $id)->first();

        if ($role->trashed()) {
            $role->restore();
        } else {
            $role->delete();
        }
    }

    public function destroy($id)
    {

        $role = Role::withTrashed()->where('_id', $id)->first();
        $role->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
