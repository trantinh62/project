<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|alpha|min:1|max:20',
            'last_name' => 'required|alpha|min:1|max:20',
            'email' => 'unique:users,email|required|email',
            'password' => 'required|min:8',
            'image' => 'nullable|mimes:jpg,png,jpeg|max:3048',
        ];
    }
}
