<?php

namespace App\Http\Requests\Client;

use App\Enums\IsMainEnum;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CreateClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'nullable|exists:params,id',
            'note' => 'nullable|string',
            
            'phones'=>'nullable|array',
            'phones.*.phone'=>'required|numeric',
            'phones.*.isMain'=>['required',new Enum(IsMainEnum::class)],
            'phones.*.countryCode'=>'required|string',

            'emails'=>'nullable|array',
            'emails.*.isMain'=>['required',new Enum(IsMainEnum::class)],
            'emails.*.email'=>'required|email|max:255',

            'addresses'=>'nullable|array',
            'addresses.*.address'=>'required|string',
            'addresses.*.isMain'=>['required',new Enum(IsMainEnum::class)],
            "addresses.*.city"=>['nullable','string'],
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

            'name.required' => __('validation.custom.required')
        ];
    }

}
