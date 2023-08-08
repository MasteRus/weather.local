<?php

namespace App\Http\Requests;

class LocationAverageRequest extends ApiFormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_date'  => 'required|date_format:Y-m-d H:i:s',
            'finish_date' => 'required|date_format:Y-m-d H:i:s',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'start_date'  => 'Date must be in format "Y-m-d H:i:s"',
            'finish_date' => 'Date must be in format "Y-m-d H:i:s"',
        ];
    }
}
