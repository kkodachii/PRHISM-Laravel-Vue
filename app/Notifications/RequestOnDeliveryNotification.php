<?php

namespace App\Notifications;

use App\Models\Requests;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestOnDeliveryNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Requests $requestEntry, public String $deliveryId)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Supply Request is on delivery!')
                    ->view('emails.request_delivered_notification', [
                        'requestEntry' => $this->requestEntry,
                        'deliveryId' => $this->deliveryId,
                        'url' => url('/inventory'),
                    ]);
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'id'=> $this->requestEntry->id,
            'action'=> 'Supplies are On Delivery',
            'title'=>'Supply Request is sent on delivery',
            'message'=>'Please confirm the delivery only after you have received the Supplies. Thank you!',
            'request_id'=> $this->requestEntry->id,
            'delivery_id', $this->deliveryId,
        ];
    }
}
