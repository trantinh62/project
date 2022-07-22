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
        $data = $request->all();
        $img = $request->image;
        $data['password'] = bcrypt($data['password']);
        if (!empty($data['image'])) {
            $data['image'] = rand() . '.' . $data['image']->getClientOriginalName();
        }
        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;
        if ($user) {
            if ($request->hasFile('image')) {
                $nameImage = $data['image'];
                $img->storeAs('images', $nameImage, 'public');

                return response()->json([
                    'data' => $user,
                    'code_token' => $token,
                    'status' => true,
                    'links' => [
                        'site' => asset('storage/images/' . $nameImage),
                        'folder' => storage_path('images/' . $nameImage),
                    ],
                ]);
            } else {

                return response()->json([
                    'data' => $user,
                    'code_token' => $token,
                    'status' => true,
                ]);
            }
        }
    }
}