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
            if ($user) {
                if ($request->hasFile('image')) {
                    $nameImage = $data['image'];
                    $img->storeAs('images', $nameImage, 'public');
                    $user['link'] = [
                        'site' => asset('storage/images/' . $nameImage),
                        'folder' => storage_path('images/' . $nameImage),
                    ];
                    return response()->json([
                        'data' => $user,
                        'code_token' => $token,
                        'status' => true
                    ]);
                } else {
    
                    return response()->json([
                        'data' => $user,
                        'code_token' => $token,
                        'status' => true,
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => null,
                'code'=> 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}