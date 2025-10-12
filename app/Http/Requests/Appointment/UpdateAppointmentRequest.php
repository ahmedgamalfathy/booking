<?php

namespace App\Http\Requests\Appointment;

use App\Enums\DayOfWeekEnum;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAppointmentRequest extends FormRequest
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
            'serviceId' => 'required|exists:services,id',
            'clientId' => 'required|exists:clients,id',
            'phoneId' => [
                'nullable',
                Rule::exists('phones', 'id')->where(function ($query) {
                       $query->where('model_type', 'App\Models\Client\Client');
                }),
            ],
            'emailId' => [
                'required',
                Rule::exists('emails', 'id')->where(function ($query) {
                    $query->where('model_type', 'App\Models\Client\Client');
                }),
            ],
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'date' => ['required','string','date_format:Y-m-d'],
            'note' => ['nullable','string'],
        ];
    }
         public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            ApiResponse::error('', $validator->errors(), HttpStatusCode::UNPROCESSABLE_ENTITY)
        );
    }

    public function messages()
    {
        return [
            'serviceId.required' => __('validation.custom.required'),
            'clientId.required' => __('validation.custom.required'),
            'phoneId.required' => __('validation.custom.required'),
            'startTime.required' => __('validation.custom.required'),
            'endTime.required' => __('validation.custom.required'),
            'date.required' => __('validation.custom.required'),
            'emailId.required' => __('validation.custom.required'),
        ];
    }
}
