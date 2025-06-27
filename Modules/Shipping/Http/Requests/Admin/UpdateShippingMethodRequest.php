<?php

namespace Modules\Shipping\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Shipping\Enums\ShippingMethodType;

class UpdateShippingMethodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $supportedLocales = core()->getSupportedLanguagesKeys();

        $rules = [
            'type' => ['required', Rule::in(ShippingMethodType::getConstants())],
            'status' => ['required', 'boolean'],
        ];

        foreach ($supportedLocales as $locale) {
            $rules[$locale . '.title'] = ['required', 'string', 'max:255'];
            $rules[$locale . '.description'] = ['required', 'string'];
        }

        if ($this->input('type') === ShippingMethodType::HYBRID) {
            $rules['flat_rate'] = ['required', 'numeric', 'min:0'];
            $rules['per_km_rate'] = ['required', 'numeric', 'min:0'];
            $rules['max_distance'] = ['required', 'integer', 'min:1'];
        } elseif ($this->input('type') === ShippingMethodType::LOCAL_STATE) {
            $rules['flat_rate'] = ['required', 'numeric', 'min:0'];
        } elseif ($this->input('type') === ShippingMethodType::EXTERNAL_STATE) {
            $rules['per_km_rate'] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
