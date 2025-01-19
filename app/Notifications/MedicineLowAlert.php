<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicineLowAlert extends Notification
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
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'action' =>"Medicine Alert",
            'title' =>"Medicine Entry is Low on Stock",
            'message' => "The medicine {$this->generic_name} is low on stock with only {$this->medicine->quantity} left.",
            'medicine_id' => $this->medicine->id,
            'generic_name' => $this->generic_name,
            'quantity' => $this->medicine->quantity,
        ];
    }
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Medicine is Low on Stock')
            ->view('emails.medicine_low_alert', [
                'medicine' => $this->medicine,
                'generic_name' => $this->generic_name,
                'medicine_id' => $this->medicine->id,
                'quantity' => $this->medicine->quantity,
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
