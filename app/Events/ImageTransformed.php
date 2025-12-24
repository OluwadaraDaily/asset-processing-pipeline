<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageTransformed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $uuid,
        public string $path,
        public string $originalFileName,
    ) {
        Log::info('Image transformation event', [
            'uuid' => $this->uuid,
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
        return [
            'uuid' => $this->uuid,
            'path' => $this->path,
            'url' => Storage::url($this->path),
            'status' => 'success',
            'originalFilename' => $this->originalFileName,
        ];
    }
}
