<?php

namespace App\Jobs;

use App\Events\ImageTransformationFailed;
use App\Events\ImageTransformed;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TransformImage implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $maxExceptions = 1;

    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $uuid,
        public string $path,
        public string $originalFilename,
        public int $width,
        public int $height
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Increase memory limit for large image processing
        ini_set('memory_limit', '512M');

        try {
            Log::info('Image transformation STARTED', [
                'path' => $this->path,
                'width' => $this->width,
                'height' => $this->height,
            ]);

            // Verify file exists
            if (! Storage::disk('public')->exists($this->path)) {
                throw new Exception("Image file not found: {$this->path}");
            }

            // Get file size for logging
            $fileSize = Storage::disk('public')->size($this->path);
            Log::info('Processing image', [
                'size_bytes' => $fileSize,
                'size_mb' => round($fileSize / 1024 / 1024, 2),
            ]);

            // Get image
            $image = Storage::disk('public')->get($this->path);

            // Transform image
            $manager = new ImageManager(new Driver);

            $imageToBeTransformed = $manager->read($image);

            // Free up memory
            unset($image);

            $imageToBeTransformed->scale($this->width, $this->height);

            // Encode file
            $extension = pathinfo($this->path, PATHINFO_EXTENSION);
            $encodedImage = match (strtolower($extension)) {
                'png' => $imageToBeTransformed->toPng(),
                'gif' => $imageToBeTransformed->toGif(),
                'webp' => $imageToBeTransformed->toWebp(),
                default => $imageToBeTransformed->toJpeg(),
            };

            // Free up memory
            unset($imageToBeTransformed);

            // Save to replace original image
            Storage::disk('public')->put($this->path, $encodedImage);

            Log::info('Image transformation completed', [
                'uuid' => $this->uuid,
                'path' => $this->path,
            ]);

            // Fire event
            event(new ImageTransformed($this->uuid, $this->path, $this->originalFilename));
        } catch (Exception $e) {
            Log::error('Image transformation failed', [
                'uuid' => $this->uuid,
                'path' => $this->path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                event(new ImageTransformationFailed($this->uuid, $this->path, $this->originalFilename, $e->getMessage()));
            }

            // Re-throw to mark job as failed (triggers auto-retry by Laravel)
            throw $e;
        }

    }
}
