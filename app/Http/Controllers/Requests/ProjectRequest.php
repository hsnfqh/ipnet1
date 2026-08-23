<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && \App\Helpers\ScopeHelper::canManageProjectsAndTasks(auth()->user());
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:Planning,On Progress,Completed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama project wajib diisi.',
            'client.required' => 'Nama client wajib diisi.',
            'location.required' => 'Lokasi project wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.after_or_equal' => 'Deadline harus setelah atau sama dengan tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}