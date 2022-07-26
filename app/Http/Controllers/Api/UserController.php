<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public const IMG_AVATAR = 1;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::all();

        return response()->json([
            'data' => $users,
            'status' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->all();
        $img = $request->image;
        $data['password'] = bcrypt($data['password']);
        if ($request->hasFile('image')) {
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view', User::class);
        $profile = User::findOrFail($id);
        try {

            return response()->json([
                'message' => 'show data User',
                'data' => $profile,
                'status' => true,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'error',
                'data' => null,
                'status' => false,
            ]);
        }
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
        $this->authorize('update', User::class);
        $profile = User::findOrFail($id);
        $data = $request->all();
        $img = $request->image;
        $path = 'public/images/';
        $imageAvatar = $profile['image'];
        $data['password'] = bcrypt($data['password']);
        if (!empty($data['image'])) {
            $data['image'] = rand() . '.' . $data['image']->getClientOriginalName();
        }
        try {
            $profile->update($data);
            if ($request->hasFile('image')) {
                Storage::delete($path . $imageAvatar);
                $nameImage = $request->image->getClientOriginalName();
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
        if (empty($request->image)) {
            if ($request->image_avatar != self::IMG_AVATAR) {
                Storage::delete($path . $imageAvatar);
                $profile['image'] = null;
                $profile->update($data);
            }
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
        $this->authorize('delete', User::class);
        $profile = User::find($id);
        try {
            $profile->delete();

            return response()->json([
                'data' => null,
                'code'=> 200,
                'message' => 'Delete User oke'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'data' => null,
                'code'=> 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}