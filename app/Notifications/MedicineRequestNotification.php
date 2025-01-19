<?php

namespace App\Notifications;

use App\Models\Requests;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class MedicineRequestNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(public Requests $medicineRequest, public string $userName, public string $barangay_name)
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
            ->subject('New Medicine Request')
            ->view(
                'emails.medicine_request_notification',
                [
                    'medicineRequest' => $this->medicineRequest,
                    'userName' => $this->userName,
                    'barangay_name' => $this->barangay_name,
                    'url' => url('/delivery'),
                ]
            );
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
            'id' => $this->medicineRequest->id,
            'action' => "Receive Request",
            'title' =>"There is New Supply Request",
            'message' => "{$this->userName} from {$this->barangay_name} has sent a new supply request.",
            'rhu_id' => $this->medicineRequest->rhu_id,
            'user_id' => $this->medicineRequest->requester_user_id,
            'user_name' => $this->userName,
            'barangay_id' => $this->medicineRequest->barangay_id,
            'requested_at' => $this->medicineRequest->requested_at,
        ];
    }
}
