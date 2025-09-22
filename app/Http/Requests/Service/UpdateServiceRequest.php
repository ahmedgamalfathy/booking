<?php

namespace App\Http\Requests\Service;

use App\Enums\TypeEnum;
use App\Enums\StatusEnum;
use App\Enums\DayOfWeekEnum;
use App\Enums\IsAailableEnum;
use App\Enums\ActionStatusEnum;
use App\Rules\SessionTimeValidation;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'name'=>['required','string','unique:services,name,'.$this->route('service')],
            'color' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'status' => ['required',new Enum(StatusEnum::class)],
            'type' => ['required'   ,new Enum(TypeEnum::class)],
            'path'=>'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:5120',

            'days' => 'required|array',
            'days.*.times.*.actionStatus' => ['required',new Enum(ActionStatusEnum::class)],
            'days.*.times.*.timeId' => [
                'nullable',
                'exists:times,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $serviceId = $this->route('service');
                        $time = \App\Models\Time\Time::find($value);
                        if ($time && $time->service_id != $serviceId) {
                            $fail("The time ID does not belong to this service.");
                        }
                    }
                }
            ],
            'days.*.times.*.startTime' => 'required|date_format:H:i',
            'days.*.times.*.endTime' =>  [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $parts     = explode('.', $attribute);
                    $dayIndex  = $parts[1];
                    $timeIndex = $parts[3];
                    $start = request()->input("days.$dayIndex.times.$timeIndex.startTime");
                    if ($start && $value <= $start) {
                        $fail("The end time must be after the start time.");
                    }
                }
           ],
            'days.*.dayOfWeek' => [
                'required',
                'string',
                new Enum(DayOfWeekEnum::class),
            ],
            'days.*.times.*.sessionTime' =>[
                    'required',
                    'integer',
                    'min:13',
                    function ($attribute, $value, $fail) {
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
            'exceptions.*.actionStatus' => ['required',new Enum(ActionStatusEnum::class)],
            'exceptions.*.isAvailable' =>[ 'required',new Enum (IsAailableEnum::class)],
            'exceptions.*.exceptionId'=>[
                'nullable',
                'exists:exceptions,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $serviceId = $this->route('service');
                        $exception = \App\Models\Exception\Exception::find($value);
                        if ($exception && $exception->service_id != $serviceId) {
                            $fail("The exception ID does not belong to this service.");
                        }
                    }
                }
            ],
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
            'exceptions.*.sessionTime' =>[
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
            ],
        ];
    }
}
