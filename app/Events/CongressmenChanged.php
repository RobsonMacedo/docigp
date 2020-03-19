<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Events\Traits\RateLimited;

class CongressmenChanged extends Broadcastable
{
    use Dispatchable, InteractsWithSockets, SerializesModels, RateLimited;

    public $congressmanId;

    /**
     * Create a new congressmanBudget instance.
     *
     * @param $congressmanId
     */
    public function __construct($congressmanId = null)
    {
        $this->congressmanId = $congressmanId;
    }

    public function broadcastChannelName()
    {
        return 'congressmen';
    }
}
