<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicineExpiringAlert extends Notification
{
    use Queueable;
    protected $medicine;
    protected $generic_name;

    /**
     * Create a new notification instance.
     */

    public function __construct($medicine, $generic_name)
    {
        $this->medicine = $medicine;
        $this->generic_name = $generic_name;
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
    
    public function toDatabase($notifiable)
    {
        return [
            'action' =>"Medicine Alert",
            'title' =>"Medicine Entry is Expiring Soon",
            'message' => "The medicine {$this->generic_name} is expiring soon on {$this->medicine->expiration_date}.",
            'medicine_id' => $this->medicine->id,
            'generic_name' => $this->generic_name,
            'expiry_date' => $this->medicine->expiry_date,
            'quantity' => $this->medicine->quantity,
        ];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Medicine Expiry Alert')
            ->view('emails.medicine-expiring-alert', [
                'medicine' => $this->medicine,
                'generic_name' => $this->generic_name,
                'url' => url('/medicines'),
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
}
