<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Helpers\Pages;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $users = User::when(!empty($request->keyword), function($query) use ($request){
            $query->where('name', 'like', '%'.$request->keyword.'%');
        })
        ->take(10)
        ->get();

        return response()->json([
            'type' => 'success',
            'data' => $users
        ], 200);
    }

    public function index(Request $request)
    {
        $ordering = json_decode($request->ordering);
        $users = User::withTrashed()
                        ->with(['role'])
                        ->where(function($where) use ($request){

                            if (!empty($request->keyword)) {
                                $where->where('name', 'like', '%'.$request->keyword.'%')
                                ->orWhere('email', 'like', '%'.$request->keyword.'%')
                                ->orWhereHas('role', function($q) use ($request){
                                    $q->where('name', 'like','%'.$request->keyword.'%');
                                });
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

        $pages = Pages::generate($users);

        return response()->json([
            'type' => 'success',
            'message' => 'fetch data stock in success!',
            'data' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'pages' => $pages,
                'data' => $users->all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required|confirmed|min:6',
            'email' => 'required|unique:users',
            'role_id' => 'required'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->role_name = $request->role_name;
        $user->role_id = $request->role_id;
        $user->phone_number = $request->phone_number;
        $user->place_of_birth = $request->place_of_birth;
        $user->date_of_birth = Carbon::parse($request->date_of_birth);
        $user->address = $request->address;
        
        if (!empty($request->file('photo'))) {

            $file = $request->file('photo');
            $file_extension = $file->getClientOriginalExtension();
            $filename = rand(0, 99).time().'.'.$file_extension;
            $file->storeAs('public/images', $filename);
            $user->photo = $filename;

        }

        $user->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil disimpan!'
        ], 201);

    }


    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id.',_id',
            'role_id' => 'required'
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        
        if (!empty($request->password)) {
            
            $request->validate([
                'password' => 'required|confirmed|min:6',
            ]);

            $user->password = Hash::make($request->password);
        }

        $user->email = $request->email;
        $user->role_name = $request->role_name;
        $user->role_id = $request->role_id;
        $user->phone_number = $request->phone_number;
        $user->place_of_birth = $request->place_of_birth;
        $user->date_of_birth = $request->date_of_birth;
        $user->address = $request->address;
        
        if (!empty($request->file('photo'))) {

            if (!empty($user->photo)) {
                if (Storage::disk('images')->exists($user->photo)) {
                    Storage::disk('images')->delete($user->photo);
                }
            }

            $file = $request->file('photo');
            $file_extension = $file->getClientOriginalExtension();
            $filename = rand(0, 99).time().'.'.$file_extension;
            $file->storeAs('public/images', $filename);
            $user->photo = $filename;

        }

        $user->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil diubah!'
        ], 201);

    }

    public function show($id)
    {
        $user = User::with(['role'])->findOrFail($id);

        return response()->json([
            'type' => 'success',
            'data' => $user
        ], 200);
    }

    public function toggle($id, Request $request)
    {
        $user = User::withTrashed()->where('_id', $id)->first();

        if ($user->trashed()) {
            $user->restore();
        } else {
            $user->delete();
        }
    }

    public function destroy($id)
    {

        $user = User::withTrashed()->where('_id', $id)->first();
        
        if (!empty($user->photo)) {
            if (Storage::disk('images')->exists($user->photo)) {
                Storage::disk('images')->delete($user->photo);
            }
        }

        $user->forceDelete();

        return response()->json([
            'type' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
