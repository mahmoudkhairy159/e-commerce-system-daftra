<?php

namespace Modules\Admin\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Admin\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:256', 'unique:roles,name,' .$this->route('role')],
            'description' => ['nullable', 'string', 'min:3', 'max:600'],
            'permission_type' => ['required', 'in:' . Role::PERMISSION_TYPE_ALL . ',' . Role::PERMISSION_TYPE_CUSTOM],
            'permissions' => ['array', 'required_if:permission_type,' . Role::PERMISSION_TYPE_CUSTOM]
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
            'statusCode'=>422
        ], 422));
    }
}
