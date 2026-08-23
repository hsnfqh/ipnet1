<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,' . $userId,
            'phone'              => 'nullable|string|max:20',
            'position'           => 'nullable|string|max:100',
            'role'               => 'required|string',
            'status'             => 'required|in:Active,Inactive',
            'division_id'        => 'nullable|exists:divisions,id',
            'team_id'            => 'nullable|exists:teams,id',
            'level'              => 'nullable|string|max:50',
            'password'           => $userId ? 'nullable|string|min:6' : 'required|string|min:6',
            'certification_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
