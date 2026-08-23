<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && \App\Helpers\ScopeHelper::canManageProjectsAndTasks(auth()->user());
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:users,id',
            'priority' => 'required|in:High,Medium,Low',
            'deadline' => 'required|date',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul task wajib diisi.',
            'project_id.required' => 'Project wajib dipilih.',
            'project_id.exists' => 'Project tidak valid.',
            'engineer_id.required' => 'Engineer wajib dipilih.',
            'engineer_id.exists' => 'Engineer tidak valid.',
            'priority.required' => 'Priority wajib dipilih.',
            'priority.in' => 'Priority tidak valid.',
            'deadline.required' => 'Deadline wajib diisi.',
        ];
    }
}