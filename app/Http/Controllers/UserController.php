<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::where('id', '!=', FacadesAuth::user()->id)->get();
        return response()->json([
            'data' => $users,
            'status' => 'success',
            'message' => 'Okay'
        ], 200);
    }
}
