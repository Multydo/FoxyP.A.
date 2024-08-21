<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class settingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
        'work_from' => 'required|date_format:H:i:s',
        'work_to' => 'required|date_format:H:i:s',
        'break_time' => 'required|date_format:H:i:s',
        'time_zone' => 'required|integer',
        'logic' => 'required|string',
        'max_app' => 'required|integer',
        'monday' => 'required|string',
        'tuesday' => 'required|string',
        'wednesday' => 'required|string',
        'thursday' => 'required|string',
        'friday' => 'required|string',
        'saturday' => 'required|string',
        'sunday' => 'required|string',
        'max_duration_switch' => 'required|boolean',
        'max_duration_time' => 'required|date_format:H:i:s',
        'min_time_switch' => 'required|boolean',
        'min_time' => 'required|date_format:H:i:s',
        'app_fixed_duration_switch' => 'required|boolean',
        'app_fixed_duration' => 'required|date_format:H:i:s',
        'allow_dm' => 'required|boolean',
    ];
    }
}