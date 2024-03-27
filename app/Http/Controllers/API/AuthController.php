<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validator  = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
        ]);
        
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $request['password']        = Hash::make($request['password']);
        $request['remember_token']  = Str::random(10);

        $user       = User::create($request->toArray());
        return response(['message' => 'User created successfully!', 'data' => $user], 200);
    }
    

    public function login(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'email'     => 'required|string|email|max:255',
            'password'  => 'required|string|min:6',
        ]);

        if ($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user   = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $user['token']  = $user->createToken('Laravel Password Grant Client')->accessToken;
                return response(['message' => 'User login successfully!', 'data' => $user], 200);
            } else {
                return response(['message' => "Email and password doesn't match.", 'data' => []], 422);
            }
        } else {
            return response(['message' => "User does not exist.", 'data' => []], 422);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response(['message' => 'You have been successfully logged out!', 'data' => []], 200);
    }
}
