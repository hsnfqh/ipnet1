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
            'category'         => 'nullable|string|in:Meeting,Day Off,Lainnya',
            'project_id'       => 'nullable',
            'new_project_name' => 'required_if:project_id,other|nullable|string|max:255',
            'engineer_id'      => 'required_without:engineer_ids|nullable|exists:users,id',
            'engineer_ids'     => 'required_without:engineer_id|nullable|array|min:1',
            'engineer_ids.*'   => 'exists:users,id',
            'date'             => 'required_without:sessions|nullable|date',
            'start_time'       => 'nullable',
            'end_time'         => 'nullable',
            'location'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'sessions'         => 'nullable|array|min:1',
            'sessions.*.date'  => 'required|date',
            'sessions.*.start_time' => 'nullable',
            'sessions.*.end_time'   => 'nullable',
            'sessions.*.location'   => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('project_id', 'required|exists:projects,id', function ($input) {
            return $input->category !== 'Day Off' && $input->project_id !== 'other';
        });

        $validator->sometimes('start_time', 'required', function ($input) {
            return $input->category !== 'Day Off';
        });
    }
}
