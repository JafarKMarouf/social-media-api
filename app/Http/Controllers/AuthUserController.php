<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $data['user'] = $user;
            $data['token'] = $user->createToken('soical-media-api')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'message' => 'User Created Successfully!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
