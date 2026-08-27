<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) return false;
        $user = auth()->user();
        return $user->hasAnyRole(['Lead Engineer', 'Team Leader', 'Lead Maintenance', 'Lead Divisi', 'Group Leader', 'Direktur', 'HD / Direktur']);
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        
        $rules = [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $userId,
            'phone'       => 'nullable|string|max:20',
            'position'    => 'required|string|max:100',
            'status'      => 'required|in:Active,Inactive',
            'role'        => 'required|string|exists:roles,name',
            'division_id' => 'nullable|exists:divisions,id',
            'team_id'     => 'nullable|exists:teams,id',
            'level'       => 'nullable|string|max:50',
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['password'] = ['nullable', 'confirmed', Password::min(8)];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama user wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi untuk user baru.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}