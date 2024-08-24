<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
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
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $user = User::query()->where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $data['user'] = $user;
                    $data['token'] = $user->createToken('soical-media-api')->plainTextToken;
                    return response()->json([
                        'status' => 'success',
                        'data' => $data,
                        'message' => 'User Logged Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Password not match!',
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'This email is not register.',
                ], 403);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $id =  Auth::user()->id;
            User::find($id)->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'User is logged out Successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'faild',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
