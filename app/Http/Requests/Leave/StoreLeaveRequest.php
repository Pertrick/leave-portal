<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'status' => 'pending',
        ]);
    }   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'status' => 'required|string|in:pending',
            'applicant_comment' => 'nullable|string',
            'replacement_staff_name' => 'required|string|max:255',
            'replacement_staff_phone' => 'required|string|max:20',
            'attachment' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        // Validate file upload
                        $validator = \Illuminate\Support\Facades\Validator::make(
                            [$attribute => $value],
                            [
                                $attribute => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120'
                            ]
                        );
                        
                        if ($validator->fails()) {
                            $fail($validator->errors()->first($attribute));
                        }
                    } elseif (!is_string($value) && !is_null($value)) {
                        $fail('The attachment must be either a file upload or a valid path.');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'leave_type_id.required' => 'Please select a leave type.',
            'start_date.required' => 'Please select a start date.',
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.required' => 'Please select an end date.',
            'end_date.after_or_equal' => 'End date must be the same as or after the start date.',
            'reason.required' => 'Please provide a reason for your leave request.',
            'reason.min' => 'The reason must be at least 10 characters long.',
            'attachment.mimes' => 'The attachment must be a PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, or XLSX file.',
            'attachment.max' => 'The attachment must not exceed 5MB.',
        ];
    }
} 