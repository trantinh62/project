<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        auth()->shouldUse('sanctum');
        $this->authorize('register', User::class);
        $data = $request->all();
        $img = $request->image;
        $data['password'] = bcrypt($data['password']);
        if (!empty($data['image'])) {
            $data['image'] = rand() . '.' . $data['image']->getClientOriginalName();
        }
        try {
           
            $user = User::create($data);
            $token = $user->createToken('auth_token')->plainTextToken;
           
            if ($request->hasFile('image')) {
                $nameImage = $data['image'];
                $img->storeAs('images', $nameImage, 'public');
                $user['link'] = [
                    'site' => asset('storage/images/' . $nameImage),
                    'folder' => storage_path('images/' . $nameImage),
                ];
            } 
        } catch (Exception $e) {
            return response()->json([
                'data' => null,
                'code'=> 500,
                'message' => $e->getMessage()
            ]);
        }
        return response()->json([
            'data' => $user,
            'code_token' => $token,
            'status' => true,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email','password')))
        {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'mesage' => 'Hi ' . $user->first_name . ', welcome to home',
            'access_token' => $token,
            'token_type'=>'bearer',
        ]);
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
          $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'logout success!',
            'code' => 200,
            'data' => null ]);
    }
}
