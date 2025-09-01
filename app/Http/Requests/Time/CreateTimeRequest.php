<?php

namespace App\Http\Requests\Time;

use App\Enums\DayOfWeek;
use App\Enums\DayOfWeekEnum;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateTimeRequest extends FormRequest
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
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'dayOfWeek' => [
                'required',
                'string',
                new Enum(DayOfWeekEnum::class)
            ],
            'sessionTime' => 'required|integer|min:1',
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
            'startTime.required' => __('validation.custom.required'),
            'endTime.required' => __('validation.custom.required'),
            'dayOfWeek.required' => __('validation.custom.required'),
            'sessionTime.required' => __('validation.custom.required'),
        ];
    }
}


