<?php

namespace Modules\Order\Services;

use Modules\Cart\Repositories\CartRepository;
use Modules\Shipping\Repositories\ShippingMethodRepository;
use Modules\User\Repositories\UserAddressRepository;

class OrderCalculationService
{
    protected $cartRepository;
    protected $userAddressRepository;
    protected $couponRepository;
    protected $shippingMethodRepository;

    public function __construct(
        UserAddressRepository $userAddressRepository,
        CartRepository $cartRepository,
        // CouponRepository $couponRepository,
        ShippingMethodRepository $shippingMethodRepository,
    ) {
        $this->cartRepository = $cartRepository;
        $this->userAddressRepository = $userAddressRepository;
        // $this->couponRepository = $couponRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
    }
    public function calculateOrderAmount(array $data): array
    {
        $distance= $data['distance']??0;
        $sumTotalData=$this->cartRepository->getCartSumTotal($data['user_id']);
        $data['order_address'] = $this->userAddressRepository->find($data['user_address_id']);
        $data['shipping_method'] = $this->shippingMethodRepository->find($data['shipping_method_id']);
        $data['price_amount'] = $sumTotalData['price_amount'];
        $data['original_price_amount'] = $sumTotalData['original_price_amount'];
        $data['tax_amount'] = $sumTotalData['tax_amount'];
        $data['discount_amount'] = $sumTotalData['sum_discount_amount'];
        $data['total_amount'] = $sumTotalData['sum_subtotal'];
        $data['shipping_method_amount'] = $this->shippingMethodRepository->calculateShippingMethodAmount($data['shipping_method'],$distance);

        return $data;
    }



}


