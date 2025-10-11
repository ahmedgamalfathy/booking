<?php

namespace App\Http\Requests\Service;

use App\Enums\TypeEnum;
use App\Enums\StatusEnum;
use App\Enums\DayOfWeekEnum;
use App\Helpers\ApiResponse;
use App\Enums\IsAailableEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\SessionTimeValidation;

class CreateServiceRequest extends FormRequest
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
    {//name , color , price , status , type ,path
        return [
            'name' => 'required|string|unique:services,name',
            'color' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'status' => ['required',new Enum(StatusEnum::class)],
            'type' => ['required'   ,new Enum(TypeEnum::class)],
            'path'=>'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:5120',

            'days' => 'required|array',
            'days.*.times.*.startTime' => 'required|date_format:H:i',
            'days.*.times.*.endTime' => 'required|date_format:H:i|after:days.*.startTime',
            'days.*.dayOfWeek' => [
                'required',
                'string',
                new Enum(DayOfWeekEnum::class),
            ],
            'days.*.times.*.sessionTime' =>[
                    'required',
                    'integer',
                    'min:5',
                    function ($attribute, $value, $fail) {
                    // $index = explode('.', $attribute)[1];
                    // $start = $this->input("days.$index.times.$index.startTime");
                    // $end   = $this->input("days.$index.times.$index.endTime");
                    $parts = explode('.', $attribute);
                    $dayIndex  = $parts[1];
                    $timeIndex = $parts[3];
                    $start = $this->input("days.$dayIndex.times.$timeIndex.startTime");
                    $end   = $this->input("days.$dayIndex.times.$timeIndex.endTime");
                    (new SessionTimeValidation($start, $end))
                        ->validate($attribute, $value, $fail);
                },
            ],

            'exceptions' => 'nullable|array',
            'exceptions.*.isAvailable' =>[ 'required',new Enum (IsAailableEnum::class)],
            'exceptions.*.startTime' => 'required|date_format:H:i',
            'exceptions.*.endTime' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $start = $this->input("exceptions.$index.startTime");

                    if ($start && $value <= $start) {
                        $fail("The end time must be after the start time.");
                    }
                },
            ],
            'exceptions.*.date' => ['required','string','date_format:Y-m-d'],
            // 'exceptions.*.sessionTime' =>[
            //         'required',
            //         'integer',
            //         'min:5',
            //         function ($attribute, $value, $fail) {
            //         $index = explode('.', $attribute)[1];
            //         $start = $this->input("exceptions.$index.startTime");
            //         $end   = $this->input("exceptions.$index.endTime");
            //         (new SessionTimeValidation($start, $end))
            //             ->validate($attribute, $value, $fail);
            //     },
            // ],

        ];
    }
     public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            ApiResponse::error('', $validator->errors(), HttpStatusCode::UNPROCESSABLE_ENTITY)
        );
    }
public function withValidator($validator)
{
    if ($this->has('exceptions')) {
        foreach ($this->input('exceptions') as $index => $exception) {
            if (($exception['isAvailable'] ?? null) == IsAailableEnum::AVAILABLE->value) {
                $validator->sometimes("exceptions.$index.sessionTime", [
                    'required',
                    'integer',
                    'min:13',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1];
                        $start = $this->input("exceptions.$index.startTime");
                        $end   = $this->input("exceptions.$index.endTime");

                        (new SessionTimeValidation($start, $end))
                            ->validate($attribute, $value, $fail);
                    },
                ], fn () => true);
            }
        }
    }
}


    public function messages()
    {
        return [
            'name.required' => __('validation.custom.required'),

            'times.required' => __('validation.custom.required'),
            'times.*.startTime.required' => __('validation.custom.required'),
            'times.*.endTime.required' => __('validation.custom.required'),
            'times.*.dayOfWeek.required' => __('validation.custom.required'),
            'times.*.dayOfWeek.unique'   => __('This time already exists for this day and service.'),
            'times.*.sessionTime.required' => __('validation.custom.required'),

            'exceptions.*.startTime.required' => __('validation.custom.required'),
            'exceptions.*.isAvailable.required' => __('validation.custom.required'),
            'exceptions.*.endTime.required' => __('validation.custom.required'),
            'exceptions.*.date.required' => __('validation.custom.required'),
            'exceptions.*.sessionTime.required' => __('validation.custom.required'),
        ];
    }
}
