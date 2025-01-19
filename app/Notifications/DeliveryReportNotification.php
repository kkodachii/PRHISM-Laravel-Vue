<?php

namespace App\Notifications;

use App\Models\Requests;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryReportNotification extends Notification
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
            ->subject('Supplies Request Delivered with Reported Issue')
            ->view('emails.delivery_report_notification', [
                'requestEntry' => $this->requestEntry,
                'deliveryId' => $this->deliveryId,
                'user' => $this->user,
                'barangay_name' => $this->barangay_name,
                'url' => url('/delivery'),
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
            'action'=> 'Delivery Report',
            'title'=>'Supplies Request has been Delivered with Reported Issue',
            'message'=>"{$this->user->name} from {$this->barangay_name} accepted the delivery with issues.",
            'user_id'=>$this->user->name,
            'request_id'=> $this->requestEntry->id,
            'delivery_id', $this->deliveryId,
        ];
    }
}
