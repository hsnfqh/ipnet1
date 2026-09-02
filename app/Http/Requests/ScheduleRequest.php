<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'title'            => 'required|string|max:255',
            'project_id'       => 'required',
            'new_project_name' => 'required_if:project_id,other|nullable|string|max:255',
            'engineer_id'      => 'required_without:engineer_ids|nullable|exists:users,id',
            'engineer_ids'     => 'required_without:engineer_id|nullable|array|min:1',
            'engineer_ids.*'   => 'exists:users,id',
            'date'             => 'required|date',
            'start_time'       => 'required',
            'end_time'         => 'nullable',
            'location'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('project_id', 'exists:projects,id', function ($input) {
            return $input->project_id !== 'other';
        });
    }
}
