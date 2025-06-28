<?php

declare(strict_types=1);

namespace Modules\Order\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Jobs\SendOrderPlacedEmailJob;

class SendAdminOrderNotification
{
    /**
     * Handle the event.
     *
     * @param OrderPlaced $event
     * @return void
     */
    public function handle(OrderPlaced $event): void
    {
        try {
            // Dispatch the email job to the queue
            SendOrderPlacedEmailJob::dispatch($event->order)
                ->delay(now()->addSeconds(5)); // Small delay to ensure order is fully processed

            Log::info('Order placed notification job dispatched', [
                'order_id' => $event->order->id,
                'user_id' => $event->order->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch order placed notification job', [
                'order_id' => $event->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
