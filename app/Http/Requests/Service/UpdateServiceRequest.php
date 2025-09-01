<?php

namespace App\Http\Requests\Service;

use App\Enums\TypeEnum;
use App\Enums\StatusEnum;
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
        ];
    }
}
