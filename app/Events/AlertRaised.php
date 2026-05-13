<?php

namespace App\Events;

use App\Models\Alert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertRaised implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alert;

    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'alert.raised';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->alert->id,
            'type' => $this->alert->type,
            'camera' => $this->alert->camera->name,
            'description' => $this->alert->description,
            'raised_by' => $this->alert->raisedBy->name,
            'is_emergency' => $this->alert->is_emergency,
            'created_at' => $this->alert->created_at->diffForHumans()
        ];
    }
}
