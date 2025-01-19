<?php

namespace App\Notifications;

use App\Models\Requests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestApprovedNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(public Requests $request, public string $barangay_name, public String $approvedMode)
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
                    ->subject('Your Supply Request Has Been Approved!')
                    ->view('emails.request_approved_notification', [
                        'request' => $this->request,
                        'barangay_name' => $this->barangay_name,
                        'url' => url('/requests'),
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
            'action' => "Request Approved",
            'title' =>"Supply Request has been Approved",
            'message' => "{$this->barangay_name} have Approved your Supply Request and will be on-delivery soon.",
        ];
    }
}
