<?php

namespace Modules\Order\Http\Requests\Admin\Order;

use App\Models\Order;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Order\Enums\OrderPaymentMethodEnum;
use Modules\Order\Enums\OrderPaymentStatusEnum;
use Modules\Order\Enums\OrderStatusEnum;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['required', Rule::in(OrderStatusEnum::getConstants())],
            'payment_method' => ['required', Rule::in(OrderPaymentMethodEnum::getConstants())],
            'payment_status' => ['required', Rule::in(OrderPaymentStatusEnum::getConstants())],
            'sub_total' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'order_address' => ['required', 'array'],
            'order_address.name' => ['required', 'string'],
            'order_address.phone' => ['required', 'string'],
            'order_address.email' => ['required', 'email'],
            'order_address.address' => ['required', 'string'],
            'order_address.city' => ['required', 'string'],
            'order_address.state' => ['required', 'string'],
            'order_address.country' => ['required', 'string'],
            'order_address.zip_code' => ['required', 'string'],
            'shipping_method' => ['nullable', 'array'],
            'shipping_method.id' => ['nullable', 'integer'],
            'shipping_method.name' => ['nullable', 'string'],
            'shipping_method.price' => ['nullable', 'numeric', 'min:0'],
            'coupon' => ['nullable', 'array'],
            'coupon.code' => ['nullable', 'string'],
            'coupon.discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],

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
