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
    protected function saveDataUser(Request $request,  $user = null )
    {
        $this->authorize('create', User::class);
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $uploaded = false;
        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $newName = rand() . '.' . $image->getClientOriginalName();
                $data['image'] = $newName;
                $uploaded = Storage::putFileAs('images', $image, $newName, 'public');
            }
            if(!$user) {
                $user = User::create($data);
            } else {
                $user->update($data);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            if ($uploaded) {
                $user['image_link'] = Storage::url('images/' . $newName);
            }
            if (empty($request->image)) {
                $profile = User::findOrFail($user->id);
                $path = 'public/images/';
                $imageAvatar = $profile['image'];
                if ($request->image_avatar != UserController::IMG_AVATAR) {
                    Storage::delete($path . $imageAvatar);
                    $profile['image'] = null;
                    $profile->update($data);
                }
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
        return $this->saveDataUser($request);
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
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        return $this->saveDataUser($request, $user);
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
