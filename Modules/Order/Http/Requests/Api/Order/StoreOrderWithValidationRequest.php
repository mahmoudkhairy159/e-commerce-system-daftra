<?php

namespace Modules\Order\Http\Requests\Api\Order;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Repositories\CartRepository;
use Modules\Order\Services\OrderValidationService;

class StoreOrderWithValidationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:255',
            'user_address_id' => 'required|exists:user_addresses,id',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'code' => 'nullable|exists:coupons,code',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateCartAndProducts($validator);
        });
    }

    /**
     * Validate cart and products
     */
    protected function validateCartAndProducts($validator)
    {
        try {
            $userId = Auth::guard('user-api')->id();
            if (!$userId) {
                $validator->errors()->add('user', 'User not authenticated');
                return;
            }

            // Get repositories through app container
            $cartRepository = app(CartRepository::class);
            $orderValidationService = app(OrderValidationService::class);

            // Get user's cart
            $cart = $cartRepository->getCartByUserId($userId);

            if (!$cart || $cart->cartProducts->isEmpty()) {
                $validator->errors()->add('cart', __('validation.cart_empty'));
                return;
            }

            // Validate each cart product
            foreach ($cart->cartProducts as $cartProduct) {
                if (!$orderValidationService->hasStock($cartProduct->product_id, $cartProduct->quantity)) {
                    $availableStock = $orderValidationService->getAvailableStock($cartProduct->product_id);
                    $validator->errors()->add(
                        "cart.product.{$cartProduct->product_id}",
                        __('validation.insufficient_stock', [
                            'requested' => $cartProduct->quantity,
                            'available' => $availableStock
                        ])
                    );
                }
            }

            // Check cart integrity
            $integrityCheck = $orderValidationService->validateCartIntegrity($cart);
            if ($integrityCheck['has_issues']) {
                foreach ($integrityCheck['issues'] as $issue) {
                    $validator->errors()->add(
                        "cart.integrity.{$issue['product_id']}",
                        $issue['message']
                    );
                }
            }

        } catch (\Exception $e) {
            $validator->errors()->add('validation', __('validation.order_validation_failed'));
        }
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
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'message' => 'Order Validation Failed',
            'statusCode' => 422,
        ], 422));
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_address_id.required' => 'Please select a delivery address.',
            'user_address_id.exists' => 'The selected delivery address is invalid.',
            'shipping_method_id.required' => 'Please select a shipping method.',
            'shipping_method_id.exists' => 'The selected shipping method is invalid.',
            'code.exists' => 'The coupon code is invalid or expired.',
        ];
    }
}
