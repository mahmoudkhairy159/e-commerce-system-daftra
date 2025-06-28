<?php

declare(strict_types=1);

namespace Modules\Order\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Order\Models\Order;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     *
     * @var Order
     */
    public Order $order;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $order = $this->order->load(['user', 'orderProducts.product']);

        return (new MailMessage)
            ->subject(__('order::app.notifications.order_placed.subject', ['order_id' => $order->id]))
            ->markdown('order::emails.admin.order_placed', [
                'order' => $order,
                'admin' => $notifiable,
                'logoUrl' => asset('logo2.png'),
                'websiteName' => 'Daftra',
                'orderUrl' => config('app.admin_url') . '/orders/' . $order->id,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'amount' => $this->order->amount,
            'status' => $this->order->status,
            'created_at' => $this->order->created_at,
        ];
    }
}
