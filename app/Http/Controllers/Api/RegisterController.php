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
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = hash::make($request->password);
        $user->role_id = $request->role_id;
        $image = $request->image;
        if (!empty($image))
        {
            $user->image = $image->getClientOriginalName();
        }
        if($user->save()){
            if (!empty($image))
            {
                $image->move('storage/user/avatar', $image->getClientOriginalName());
            }
        }

       $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json([
        'data' => $user,
        'code_token' => $token,
        'status' => true,
       ]);


    }
}
