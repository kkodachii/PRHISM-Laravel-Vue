<?php

namespace App\Notifications;

use App\Models\Requests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryReturnNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(public Requests $request, public string $barangay_name, public string $reason)
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
        // Using a Blade view to create the email content
        return (new MailMessage)
            ->subject('Delivery Return Request')
            ->view('emails.medicine_return_notification', [
                'request' => $this->request,
                'barangay_name' => $this->barangay_name,
                'reason' => $this->reason,
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
            'id' => $this->request->id,
            'request_id' => $this->request->id,
            'action' => "Delivery Return",
            'title' =>"Supply Delivery has been requested for return",
            'message' => "{$this->barangay_name} have requested for return for the reason: {$this->reason}",
        ];
    }
}
