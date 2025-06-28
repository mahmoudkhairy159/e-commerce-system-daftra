@component('mail::message')
# {{ __('order::app.notifications.order_placed.greeting', ['admin' => $admin->name]) }}

{{ __('order::app.notifications.order_placed.intro') }}

## {{ __('order::app.notifications.order_placed.order_details') }}

@component('mail::panel')
**{{ __('order::app.notifications.order_placed.order_id') }}:** #{{ $order->id }}<br>
**{{ __('order::app.notifications.order_placed.customer') }}:** {{ $order->user->name }}<br>
**{{ __('order::app.notifications.order_placed.email') }}:** {{ $order->user->email }}<br>
**{{ __('order::app.notifications.order_placed.amount') }}:** {{ number_format($order->total_amount, 2) }} {{ config('app.currency', 'USD') }}<br>
**{{ __('order::app.notifications.order_placed.date') }}:** {{ $order->created_at->format('Y-m-d H:i:s') }}
@endcomponent







{{ __('order::app.notifications.order_placed.outro') }}

{{ __('app.thanks') }},
{{ $websiteName }}
@endcomponent
