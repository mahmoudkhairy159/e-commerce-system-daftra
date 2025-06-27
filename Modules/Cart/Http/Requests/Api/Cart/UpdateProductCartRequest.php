<?php

namespace Modules\Cart\Http\Requests\Api\Cart;


use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cart\Models\CartProduct;
use Modules\Product\Models\Product;

class UpdateProductCartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                $this->validateProductStock(),
            ],

        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'expires_at' => $this->input('expires_at', Carbon::now()->addDays(1)->toDateTimeString()),
        ]);
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

    /**
     * Validate the product stock.
     *
     * @return \Closure
     */
    protected function validateProductStock(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $cartProductId = $this->route('id');

            // Ensure the cart product exists
            $cartProduct = CartProduct::find($cartProductId);
            if (!$cartProduct) {
                return $fail(__('validation.exists', ['attribute' => 'cart_product']));
            }

            // Fetch the associated product
            $product = Product::find($cartProduct->product_id);
            if (!$product) {
                return $fail(__('validation.exists', ['attribute' => 'product_id']));
            }

            // Validate quantity against product stock
            if ($value > $product->stock) {
                $fail(__('validation.quantity_exceeded', [
                    'stock' => $product->stock,
                ]));
            }
        };
    }
}
