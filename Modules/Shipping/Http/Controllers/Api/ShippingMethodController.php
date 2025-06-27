<?php

namespace Modules\Shipping\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Traits\CacheTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Shipping\Models\ShippingMethod;
use Modules\Shipping\Transformers\Admin\ShippingMethodResource;
use Modules\Shipping\Repositories\ShippingMethodRepository;
use Modules\Shipping\Filters\ShippingMethodFilter;

class ShippingMethodController extends Controller
{
}
