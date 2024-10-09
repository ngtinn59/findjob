<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'phone_number' => 'required',
            'date_of_birth' => 'required|date',
            'address' => 'required',
            'introduction' => 'required',
            'universities_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'major' => 'required',
            'grade' => 'required',
            'skills_id' => 'required',
            'introduce' => 'required',
            'companies_id' => 'required',
            'project_name' => 'required',
            'project_date' => 'required|date',
            'description' => 'required',
        ];
    }
}
