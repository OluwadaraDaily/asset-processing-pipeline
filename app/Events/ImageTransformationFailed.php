<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImageTransformationFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $uuid,
        public string $path,
        public string $originalFilename,
        public string $errorMessage
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('image-transformations'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'uuid' => $this->uuid,
            'path' => $this->path,
            'errorMessage' => $this->errorMessage,
            'originalFilename' => $this->originalFilename,
            'status' => 'error',
        ];
    }
}
