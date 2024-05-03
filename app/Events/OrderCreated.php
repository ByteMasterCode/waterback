<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public string $title;
    public string $message;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->title = 'New Order Created';
        $this->message = "Order #{$order->id} has been created successfully.";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('order-created');
    }
}
