<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profile = User::find(Auth::id())->toArray();
        
        return response()->json([
            'message' => 'show data User',
            'data' => $profile,
            'status' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $profile = User::findOrFail($id);
        $data = $request->all();
        $img = $request->image;
        $path = 'public/images/';
        $imageAvatar =  $profile['image'];
        $data['password'] = bcrypt($data['password']);
        if (!empty($data['image'])) {
            $data['image'] = rand() . '.' . $data['image']->getClientOriginalName();
        }
        try {
            $profile->update($data);
            if ($request->hasFile('image')) {
                Storage::delete($path . $imageAvatar);
                $nameImage = $data['image'];
                $img->storeAs('images', $nameImage, 'public');
                $profile['link'] = [
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
        if ($request->imageAvatar == null) {
            Storage::delete($path . $imageAvatar);
            $profile['image'] = null;
            $profile->update($data);
        }
        return response()->json([
            'data' => $profile,
            'status' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
