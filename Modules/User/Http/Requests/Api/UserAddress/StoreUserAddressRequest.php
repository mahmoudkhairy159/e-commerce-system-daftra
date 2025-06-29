<?php
namespace Modules\User\Http\Requests\Api\UserAddress;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\User\Enums\UserAddressEnum;

class StoreUserAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
           
            'zip_code' => [
                'nullable',
                'string',
                'max:10', // Adjust the max length based on your requirements
            ],
            'address' => [
                'required',
                'string',
                'max:500', // Adjust the max length based on your requirements
            ],
            'type' => [
                'required',
                Rule::in(UserAddressEnum::getConstants()),
            ],
            'is_default' => [
                'nullable',
                'boolean',
            ],
            'phone_code' => [
                'required_with:phone',
                'string',
                'max:10',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:15',
            ],
            'title' => [
                'nullable',
                'string',
                'max:100',
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'min:-180',
                'max:180',
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'min:-90',
                'max:90',
            ],
        ];

    }


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation Error',
            'statusCode' => 422,
        ], 422));
    }
}