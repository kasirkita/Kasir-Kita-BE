<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function check()
    {
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

        $api_token = Str::random(25);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->api_token = hash('sha256', $api_token);
        $user->save();

        return response()->json([
            'type' => 'success',
            'message' => 'Registrasi berhasil',
            'token' => $api_token,
            'data' => $user
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
            
            $user->forceFill([
                'api_token' => hash('sha256', $token)
            ])->save();
            
            return response()->json([
                'type' => 'success',
                'message' => 'Login Berhasil',
                'token' => $token,
                'data' => $user
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
}
