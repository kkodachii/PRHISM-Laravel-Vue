<?php

namespace App\Events;

use App\Models\Requests;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

class RequestReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public DatabaseNotification $notication, public requests $request, public User $rhuSuperAdmin, public User $userID, public String $barangay_name )
    {
        //
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notication->id,
            'action' => "Receive Request",
            'title' => "There is New Supply Request",
            'message' => "{$this->userID->name} from {$this->barangay_name} has sent a new supply request.",
            'rhu_id' => $this->request->rhu_id,
            'user_id' => $this->request->requester_user_id,
            'user_name' => $this->userID->name,
            'barangay_id' => $this->request->barangay_id,
            'requested_at' => $this->request->requested_at,
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
            new PrivateChannel("App.Models.User.{$this->rhuSuperAdmin->id}"),
        ];
    }
}
