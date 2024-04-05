<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_date'  => 'required|date|date_format:Y-m-d H:i:s',
            'finish_date' => 'required|date|date_format:Y-m-d H:i:s|after:start_date',
        ];
    }
}
