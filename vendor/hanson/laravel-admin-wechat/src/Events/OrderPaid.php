<?php

namespace Hanson\LaravelAdminWechat\Events;

use Hanson\LaravelAdminWechat\Models\WechatOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var WechatOrder
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @param WechatOrder $order
     */
    public function __construct(WechatOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
