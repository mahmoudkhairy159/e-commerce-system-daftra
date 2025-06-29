<?php

namespace Modules\Order\Http\Requests\Admin\Order;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Order\Enums\OrderPaymentMethodEnum;
use Modules\Order\Enums\OrderPaymentStatusEnum;
use Modules\Order\Enums\OrderStatusEnum;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'exists:users,id'],
            'status' => ['sometimes',  Rule::in(OrderStatusEnum::getConstants())],
            'payment_method' => ['sometimes', Rule::in(OrderPaymentMethodEnum::getConstants())],
            'payment_status' => ['sometimes',Rule::in(OrderPaymentStatusEnum::getConstants())],
            'sub_total' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'order_address' => ['sometimes', 'array'],
            'order_address.name' => ['sometimes', 'string'],
            'order_address.phone' => ['sometimes', 'string'],
            'order_address.email' => ['sometimes', 'email'],
            'order_address.address' => ['sometimes', 'string'],
            'order_address.zip_code' => ['sometimes', 'string'],
            'shipping_method' => ['sometimes', 'array'],
            'shipping_method.id' => ['sometimes', 'integer'],
            'shipping_method.name' => ['sometimes', 'string'],
            'shipping_method.price' => ['sometimes', 'numeric', 'min:0'],
            'coupon' => ['sometimes', 'array'],
            'coupon.code' => ['sometimes', 'string'],
            'coupon.discount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['sometimes', 'string'],
            'products' => ['sometimes', 'array'],
            'products.*.id' => ['sometimes', 'exists:order_products,id'],
            'products.*.product_id' => ['required_with:products', 'exists:products,id'],
            'products.*.quantity' => ['required_with:products', 'integer', 'min:1'],
            'products.*.price' => ['required_with:products', 'numeric', 'min:0'],
            'products.*.options' => ['nullable', 'array'],
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