<?php

namespace Modules\Order\Http\Requests\Api\Order;

use App\Models\Order;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Order\Enums\OrderStatusEnum;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $order = $this->route('order');

        // Define which statuses a user can update to
        $allowedStatusTransitions = [
            OrderStatusEnum::CANCELLED, // Users can cancel orders
            OrderStatusEnum::RETURNED   // Users can mark orders as returned
        ];

        // Define which statuses allow cancellation
        $canBeCancelled = [
            OrderStatusEnum::PENDING,
            OrderStatusEnum::PROCESSING,
            OrderStatusEnum::SHIPPED
        ];

        // Define which statuses allow marking as returned
        $canBeReturned = [
            OrderStatusEnum::DELIVERED
        ];

        return [
            'status' => [
                'sometimes',
                'string',
                Rule::in($allowedStatusTransitions),
                function ($attribute, $value, $fail) use ($order, $canBeCancelled, $canBeReturned) {
                    if ($value === OrderStatusEnum::CANCELLED && !in_array($order->status, $canBeCancelled)) {
                        $fail('You can only cancel orders with pending, processing or shipped status.');
                    }

                    if ($value === OrderStatusEnum::RETURNED && !in_array($order->status, $canBeReturned)) {
                        $fail('You can only mark delivered orders as returned.');
                    }
                },
            ],
            'notes' => ['sometimes', 'string', 'max:500'],
            'order_address' => ['sometimes', 'array'],
            'order_address.name' => ['required_with:order_address', 'string'],
            'order_address.phone' => ['required_with:order_address', 'string'],
            'order_address.email' => ['required_with:order_address', 'email'],
            'order_address.address' => ['required_with:order_address', 'string'],
            'order_address.zip_code' => ['required_with:order_address', 'string'],
            'return_reason' => [
                'required_if:status,' . OrderStatusEnum::RETURNED,
                'nullable',
                'string',
                'max:1000'
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = $this->route('order');
        return auth()->check() && $order && auth()->id() === $order->user_id;    }
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
    protected function prepareForValidation()
    {
        $order = $this->route('order');

        // Check if the order status prevents modification
        if ($order && in_array($order->status, [
            OrderStatusEnum::SHIPPED,
            OrderStatusEnum::CANCELLED,
            OrderStatusEnum::REFUNDED,
            OrderStatusEnum::RETURNED
        ])) {
            // Remove ability to update address for these statuses
            $this->request->remove('order_address');
        }

        // If attempting to cancel but the order is past cancellable status
        if ($this->request->has('status') &&
            $this->request->get('status') === OrderStatusEnum::CANCELLED &&
            !in_array($order->status, [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PROCESSING,
                OrderStatusEnum::SHIPPED
            ])) {
            $this->request->remove('status');
        }
    }
}