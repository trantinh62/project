<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id())->toArray();

        return response()->json([
            'message' => 'show data User',
            'data' => $user,
            'status' => true,
        ]);
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
            if ($request->image_avatar != UserController::IMG_AVATAR) {
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
}
