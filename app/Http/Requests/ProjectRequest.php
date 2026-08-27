<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya Team Leader / Lead Engineer yang berhak membuat/mengubah project
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
            'name'           => 'required|string|max:255',
            'client'         => 'required|string|max:255',
            'sales_name'     => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'project_type'   => 'required|string|max:100',
            'visit_schedule' => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'start_date'     => 'required|date',
            'deadline'       => 'required|date|after_or_equal:start_date',
        ];
    }
}
