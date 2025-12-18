<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageTransformed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $path,
        public string $originalFileName,
    )
    {
        Log::info('Image transformation event', [
            'path' => $this->path,
            'originalFileName' => $this->originalFileName,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('image-transformations'),
        ];
    }

    public function broadcastWith(): array 
    {
        Log::info('broadcastWith function', [
            'path' => $this->path,
            'originalFileName' => $this->originalFileName,
        ]);

        return [
            'path' => $this->path,
            'url' => Storage::url($this->path),
            'status' => 'success',
            'originalFileName' => $this->originalFileName,
        ];
    }
}
