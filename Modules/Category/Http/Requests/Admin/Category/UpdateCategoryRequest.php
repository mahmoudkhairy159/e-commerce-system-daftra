<?php

namespace Modules\Category\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $supportedLocales = core()->getSupportedLanguagesKeys();
        $rules = [];
        foreach ($supportedLocales as $locale) {
            $rules[$locale . '.name']   = ['required', 'string', 'max:255'];
            $rules[$locale . '.description']   = ['required', 'string'];
        }
        $rules['code'] = ['required', 'unique:categories,code,' . $this->route('category')];
        $rules['image'] = ['nullable','image','mimes:jpeg,png,jpg,gif', 'max:5000'];
        $rules['position'] = ['required', 'integer', 'min:1'];
        $rules['parent_id'] = ['nullable', 'integer', 'exists:categories,id'];
        $rules['status'] = ['required','in:0,1'];
      

        return $rules;
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