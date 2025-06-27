<?php

namespace Modules\Cart\Http\Requests\Api\Cart;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;
use Modules\Product\Models\Product;

class AddToCartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',


            'quantity' => [
                'required',
                'integer',
                'min:1',
                $this->validateProductStock(),
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
    protected function prepareForValidation()
    {
        $this->merge([
            'expires_at' => $this->input('expires_at', Carbon::now()->addDays(1)->toDateTimeString()),
        ]);
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
    protected function validateProductStock(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $productId = $this->input('product_id');

            // Fetch the product and check stock availability
            $product = Product::findOrFail($productId);

            if (!$product) {
                return $fail(__('validation.exists', ['attribute' => 'product_id']));
            }

            if ($value > $product->stock) {
                $fail(__('validation.quantity_exceeded', [
                    'stock' => $product->stock,
                ]));
            }
        };
    }
}
