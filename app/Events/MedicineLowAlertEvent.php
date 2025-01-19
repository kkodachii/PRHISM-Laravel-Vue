<?php

namespace App\Events;

use App\Models\Medicines;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

class MedicineLowAlertEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DatabaseNotification $notication,public User $user, public Medicines $medicine, public String $generic_name)
    {
       //
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->notication->id,
            'action' =>"Medicine Alert",
            'title' =>"Medicine Entry is Low on Stock",
            'message' => "The medicine {$this->generic_name} is low on stock with only {$this->medicine->quantity} left.",
            'medicine_id' => $this->medicine->id,
            'quantity' => $this->medicine->quantity,
            'barangay_id' => $this->medicine->barangay_id,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("App.Models.User.{$this->user->id}"),
        ];
    }


}
