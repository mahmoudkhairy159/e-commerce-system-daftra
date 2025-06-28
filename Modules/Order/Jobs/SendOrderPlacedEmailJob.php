<?php

declare(strict_types=1);

namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Admin;
use Modules\Order\Models\Order;
use Modules\Order\Notifications\OrderPlacedNotification;

class SendOrderPlacedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Order
     */
    public Order $order;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            // Get all active admins
            $admins = Admin::where('status', Admin::STATUS_ACTIVE)
                ->where('blocked', 0)
                ->whereNotNull('email')
                ->get();

            // Send notification to each admin
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new OrderPlacedNotification($this->order));

                    Log::info('Order placed notification sent to admin', [
                        'order_id' => $this->order->id,
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send order notification to admin', [
                        'order_id' => $this->order->id,
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process order placed notification job', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Order placed notification job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
