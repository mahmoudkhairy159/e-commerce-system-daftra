<?php

namespace Modules\Product\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Product\Enums\ProductTypeEnum;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $supportedLocales = core()->getSupportedLanguagesKeys();
        $rules = [];

        foreach ($supportedLocales as $locale) {
            $rules[$locale . '.name'] = ['required', 'string', 'max:255'];
            $rules[$locale . '.seo_description'] = ['required', 'string'];
            $rules[$locale . '.seo_keys'] = ['required', 'string'];
            $rules[$locale . '.short_description'] = ['required', 'string'];
            $rules[$locale . '.long_description'] = ['nullable', 'string'];
            $rules[$locale . '.return_policy'] = ['nullable', 'string'];
        }
        $rules['code'] = ['required', 'unique:products,code'];
        $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5000'];
        $rules['video_url'] = ['nullable', 'url'];
        $rules['type'] = ['nullable', Rule::in(ProductTypeEnum::getConstants())];
        $rules['position'] = ['required', 'integer', 'min:1'];
        $rules['status'] = ['required', 'in:0,1'];

        // Related products
        $rules['relatedProductIds'] = ['nullable', 'array'];
        $rules['relatedProductIds.*'] = [
            'required',
            'exists:products,id',
            'distinct',
        ];

        // Accessories
        $rules['productAccessoriesIds'] = ['nullable', 'array'];
        $rules['productAccessoriesIds.*'] = [
            'required',
            'exists:products,id',
            'distinct',
        ];

        // Categories
        $rules['categoryIds'] = ['nullable', 'array'];
        $rules['categoryIds.*'] = [
            'required',
            'exists:categories,id',
            'distinct',
        ];

        $rules['currency'] = ['required', 'string', 'max:10'];
        $rules['stock'] = ['required', 'integer', 'min:1'];
        $rules['price'] = ['required', 'numeric', 'min:0'];
        $rules['tax_rate'] = ['required', 'numeric', 'min:0','max:100'];
        $rules['offer_price'] = ['nullable', 'numeric', 'min:0'];
        $rules['offer_start_date'] = ['nullable', 'date'];
        $rules['offer_end_date'] = ['nullable', 'date', 'after_or_equal:offer_start_date'];
        $rules['approval_status'] = ['required', 'in:0,1'];

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
            'statusCode' => 422
        ], 422));
    }
}