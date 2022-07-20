<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // $error_email = User::where('email', $request->email)->count();
        // if($error_email > 0)
        // {
        //     return response()->json([
        //         'msg' => 'error email',
        //         'status' => false
        //     ]);
        // }
        $image = $request->image;
        if (!empty($image))
        {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'image' => $image->getClientOriginalName(),
                'password' => hash::make($request->password),
                'role_id' => $request->role_id
               ]);
        }

       $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json([
        'data' => $user,
        'code_token' => $token,
        'status' => true,
       ]);


    }
}
