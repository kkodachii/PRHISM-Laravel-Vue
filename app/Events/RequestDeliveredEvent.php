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

class RequestDeliveredEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public DatabaseNotification $notication, public Requests $requestEntry, public String $deliveryId, public User $rhuUser, public User $user, public String $barangay_name )
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
            'action'=> 'Request Delivered',
            'title'=>'Supplies Request has been delivered',
            'message'=>"{$this->user->name} from {$this->barangay_name} confirmed the Supplies are delivered successfully.",
            'user_id'=>$this->user->name,
            'request_id'=> $this->requestEntry->id,
            'delivery_id', $this->deliveryId,
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
            new PrivateChannel("App.Models.User.{$this->rhuUser->id}"),
        ];
    }
}
