<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\hash;
use Illuminate\Support\Facades\Storage;

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
        $nameImage = rand().'.'.$image->getClientOriginalName();
        if (!empty($image))
        {
            $user->image = $nameImage;
        }
        if($user->save())
        {
            $request->image->storeAs('images',$nameImage, 'public');
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'data' => $user,
            'code_token' => $token,
            'status' => true,
            'image' => asset('storage/images/'.$nameImage)
        ]);
    }
}