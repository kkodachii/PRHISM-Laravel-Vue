<?php

namespace App\Notifications;

use App\Models\Requests;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestDeliveredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Requests $requestEntry, public String $deliveryId, public User $user, public String $barangay_name)
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
                    ->subject('Supply Request Has Been Delivered!')
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
            'action'=> 'Request Delivered',
            'title'=>'Supplies Request has been delivered',
            'message'=>"{$this->user->name} from {$this->barangay_name} confirmed the Supplies are delivered successfully.",
            'user_id'=>$this->user->name,
            'request_id'=> $this->requestEntry->id,
            'delivery_id', $this->deliveryId,
        ];
    }
}
