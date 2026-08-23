<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && \App\Helpers\ScopeHelper::isManagerial(auth()->user());
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul jadwal wajib diisi.',
            'project_id.required' => 'Project wajib dipilih.',
            'project_id.exists' => 'Project tidak valid.',
            'engineer_id.required' => 'Engineer wajib dipilih.',
            'engineer_id.exists' => 'Engineer tidak valid.',
            'date.required' => 'Tanggal wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'location.required' => 'Lokasi wajib diisi.',
        ];
    }
}