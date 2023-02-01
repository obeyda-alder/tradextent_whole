<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class SellerConfirmNotification extends Notification
{
    use Queueable;
    
    protected $seller_confirm_notification;

    public function __construct($seller_confirm_notification)
    {
        $this->seller_confirm_notification = $seller_confirm_notification;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'shop_id'      => $this->seller_confirm_notification['shop_id'],
        ];
    }
}
