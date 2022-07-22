<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Auth::attempt($request->only('email','password')))
        {
            return response()->json([
                'message' => 'Unauthorized'
            ],401);
        }
        $user = User::where('email',$request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'mesage' => 'Hi ' . $user->first_name . ', welcome to home',
            'access_token' => $token,
            'token_type'=>'bearer',
        ]);
    }
}
