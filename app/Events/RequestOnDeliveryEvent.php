<?php

namespace App\Events;

use App\Models\Requests;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

class RequestOnDeliveryEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public DatabaseNotification $notication, public Requests $requestEntry, public String $deliveryId )
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
            'id'=> $this->notication->id,
            'action'=> 'Request is On Delivery',
            'title'=>'Supplies Request is sent on delivery',
            'message'=>'Please confirm the delivery only after you have received the Supplies. Thank you!',
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
            new PrivateChannel("App.Models.User.{$this->requestEntry->requester_user_id}"),
        ];
    }
}
