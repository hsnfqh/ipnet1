<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && \App\Helpers\ScopeHelper::canManageProjectsAndTasks(auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:users,id',
            'priority' => 'required|in:High,Medium,Low',
            'status' => 'nullable|in:Assigned,In Progress,Waiting Review,Completed',
            'deadline' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string',
        ];
    }
}
