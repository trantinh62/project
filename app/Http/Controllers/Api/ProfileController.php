<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\UserController;

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
        if ($request->hasFile('image')) {
            $data['image'] = rand() . '.' . $data['image']->getClientOriginalName();
        }
        try {
            $profile->update($data);
            if ($request->hasFile('image')) {
                Storage::delete($path . $imageAvatar);
                $nameImage = $request->image->getClientOriginalName();
                $img->storeAs('images', $nameImage, 'public');
                $profile['link'] = asset('storage/images/' . $nameImage);
            } 
        } catch (Exception $e) {
            return response()->json([
                'data' => null,
                'code'=> 500,
                'message' => $e->getMessage()
            ]);
        }
        return response()->json([
            'data' => $profile,
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
    public function store(Request $request)
    {
        $users = User::query();
        if (!empty($request->search)) {
            $users->Where('first_name', 'like', '%' . $request->search . '%');
            $users = $users->get()->toArray();
            if ($users == []) {
                return response()->json([
                    'Message' => 'Không có dữ liệu',
                    'status' => true,
                ]);
            }
            return response()->json([
                'data' => $users,
                'status' => true,
            ]);
        }

    }
}
