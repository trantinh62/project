<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public const IMG_AVATAR = 1;
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
