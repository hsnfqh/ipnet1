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

    protected function prepareForValidation(): void
    {
        if (empty($this->engineer_id) && empty($this->engineer_ids)) {
            $this->merge([
                'engineer_id' => auth()->id(),
                'engineer_ids' => [auth()->id()],
            ]);
        }
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
            'category'         => 'nullable|string|max:100',
            'project_id'       => 'nullable',
            'new_project_name' => 'required_if:project_id,other|nullable|string|max:255',
            'engineer_id'      => 'nullable|exists:users,id',
            'engineer_ids'     => 'nullable|array',
            'engineer_ids.*'   => 'exists:users,id',
            'date'             => 'required_without:sessions|nullable|date',
            'start_time'       => 'nullable',
            'end_time'         => 'nullable',
            'location'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'create_task'      => 'nullable|boolean',
            'task_priority'    => 'nullable|string|in:Low,Medium,High,Urgent',
            'sessions'         => 'nullable|array|min:1',
            'sessions.*.date'  => 'required|date',
            'sessions.*.start_time' => 'nullable',
            'sessions.*.end_time'   => 'nullable',
            'sessions.*.location'   => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('project_id', 'nullable|exists:projects,id', function ($input) {
            return $input->category !== 'Day Off' && $input->project_id !== 'other';
        });

        $validator->sometimes('start_time', 'required', function ($input) {
            return $input->category !== 'Day Off';
        });
    }
}
