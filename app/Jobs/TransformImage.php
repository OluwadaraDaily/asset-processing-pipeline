<?php

namespace App\Jobs;

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

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $path,
        public int $width,
        public int $height
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Image transformation STARTED', [
                'path' => $this->path
            ]);

            Log::info('Attempting to read file', [
                'path' => $this->path,
                'exists' => Storage::disk('public')->exists($this->path),
                'full_path' => Storage::disk('public')->path($this->path),
            ]);

            // get image
            $image = Storage::disk('public')->get($this->path);

            Log::info('Attempting to get file', [
                'image' => Storage::disk('public')->get($this->path)
            ]);

            // transform image
            $manager = new ImageManager(new Driver());

            $imageToBeTransformed = $manager->read($image);
            $imageToBeTransformed->scale($this->width, $this->height);

            // encode file
            $extension = pathinfo($this->path, PATHINFO_EXTENSION);
            $encodedImage = match(strtolower($extension)) {
                'png' => $imageToBeTransformed->toPng(),
                'gif' => $imageToBeTransformed->toGif(),
                'webp' => $imageToBeTransformed->toWebp(),
                default => $imageToBeTransformed->toJpeg(),
            };

            // save to replace original image
            Storage::disk('public')->put($this->path, $encodedImage);

            Log::info('Image transformation completed', [
                'path' => $this->path,
                'width' => $this->width,
                'height' => $this->height,
            ]);

            // fire event
            event(new ImageTransformed($this->path, ''));
        } catch(Exception $e) {
            Log::error('Image transformation failed', [
            'path' => $this->path,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            ]);

            // Optionally fire a failure event
            // event(new ImageTransformationFailed($this->path, $e->getMessage()));

            // Re-throw to mark job as failed (triggers retry)
            throw $e;
        }

    }
}
