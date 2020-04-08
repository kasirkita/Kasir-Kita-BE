<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Permission;
use Illuminate\Support\Facades\Artisan;
use App\Role;
use App\Setting;

class AuthController extends Controller
{
    public function check(Request $request)
    {
        // $users = User::where('ip_address', $request->ip())->count();
        $users = User::count();
        
        if ($users > 0) {
            $user_exists = true;
        } else {
            $user_exists = false;
        }

        return response()->json([
            'type' => 'success',
            'user_exists' => $user_exists
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        
        $permissions = Permission::get();
        $setting = Setting::get();

        if (!empty($permissions)) {
            Artisan::call('fill:permission');
        }

        if (!empty($setting)) {
            Artisan::call('fill:setting');
        }

        $role = Role::firstOrNew(['name' => 'Admin']);
        $role->save();
        $role->permissions()->delete();

        foreach (Permission::get() as $permission) {
            $role->permissions()->create([
                'type' => $permission->slug,
                'allow' => true
            ]);
        }

        $api_token = Str::random(25);

        $user = new User;
        $user->ip_address = $request->ip();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // $user->api_token = hash('sha256', $api_token);
        $user->api_token = $api_token;
        $user->role_id = $role->id;
        $user->save();

        $permissions = Permission::whereNull('parent_id')->get();
        $permission_allowed = $permissions->map(function($permission) use ($user){

        $permission_allowed = collect($user->role->permissions)->where('allow', true);

        if ($permission_allowed->pluck('type')->contains($permission->slug)) {

            return [
                '_id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'icon' => $permission->icon,
                'children' => $permission->children->map(function($child) use ($user){
                    $permission_allowed = collect($user->role->permissions)->where('allow', true);
                        if ($permission_allowed->pluck('type')->contains($child->slug)) {
                            return [
                                '_id' => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                            ];
                        }
                    })
                ];
            }
        });

        return response()->json([
            'type' => 'success',
            'message' => 'Registrasi berhasil',
            'token' => $api_token,
            'data' => $user,
            'permissions' => $permission_allowed->toArray(),
            'redirect' => $user->role->permissions->where('allow', true)->first(),
            'setting' => Setting::first()
        ], 200);

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users',
            'password' => 'required|min:6'
        ]);
        
        $token = Str::random(25);
        $user = User::where('email', $request->email)->first();
        
        if (Hash::check($request->password, $user->password)) {
            
            
            if (empty($user->api_token)) {
                
                $user->forceFill([
                    'api_token' => hash('sha256', $token)
                ])->save();
            }

            $permissions = Permission::whereNull('parent_id')->get();
            $permission_allowed = $permissions->map(function($permission) use ($user){

                $permission_allowed = collect($user->role->permissions)->where('allow', true);

                if ($permission_allowed->pluck('type')->contains($permission->slug)) {

                    return [
                        '_id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'icon' => $permission->icon,
                        'children' => $permission->children->map(function($child) use ($user){
                            $permission_allowed = collect($user->role->permissions)->where('allow', true);
                            if ($permission_allowed->pluck('type')->contains($child->slug)) {
                                return [
                                    '_id' => $child->id,
                                    'name' => $child->name,
                                    'slug' => $child->slug,
                                ];
                            }
                        })
                    ];
                }
            });
            
            return response()->json([
                'type' => 'success',
                'message' => 'Login Berhasil',
                'token' => $user->api_token,
                'data' => $user,
                'permissions' => $permission_allowed->toArray(),
                'redirect' => $user->role->permissions->where('allow', true)->first(),
                'setting' => Setting::first()
            ], 200);

        }   else {
            return response()->json([
                'type' => 'error',
                'message' => 'Silahkan cek password anda',
                'errors' => [
                    'password' => [
                        'Password anda tidak valid'
                    ]
                ]
            ], 422);
        }
    }

    public function setUrl()
    {
        return response()->json([
            'type' => 'success',
            'message' => 'Berhasil terhubung'
        ]);
    }
}
