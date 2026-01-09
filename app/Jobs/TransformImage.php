<?php

namespace App\Jobs;

use App\Events\ImageTransformationFailed;
use App\Events\ImageTransformed;
use App\Models\Image;
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
        public Image $image
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Increase memory limit for large image processing
        ini_set('memory_limit', '512M');

        // Update status to processing
        $this->image->update(['status' => 'processing']);

        try {
            Log::info('Image transformation STARTED', [
                'path' => $this->image->path,
                'width' => $this->image->target_width,
                'height' => $this->image->target_height,
            ]);

            // Verify file exists
            if (! Storage::disk('public')->exists($this->image->path)) {
                throw new Exception("Image file not found: {$this->image->path}");
            }

            // Get file size for logging
            $fileSize = Storage::disk('public')->size($this->image->path);
            Log::info('Processing image', [
                'size_bytes' => $fileSize,
                'size_mb' => round($fileSize / 1024 / 1024, 2),
            ]);

            // Get image content
            $imageContent = Storage::disk('public')->get($this->image->path);

            // Transform image
            $manager = new ImageManager(new Driver);

            $imageToBeTransformed = $manager->read($imageContent);

            // Store original dimensions if not set
            if (! $this->image->original_width || ! $this->image->original_height) {
                $this->image->update([
                    'original_width' => $imageToBeTransformed->width(),
                    'original_height' => $imageToBeTransformed->height(),
                ]);
            }

            // Free up memory
            unset($imageContent);

            $imageToBeTransformed->scale($this->image->target_width, $this->image->target_height);

            // Encode file
            $extension = pathinfo($this->image->path, PATHINFO_EXTENSION);
            $encodedImage = match (strtolower($extension)) {
                'png' => $imageToBeTransformed->toPng(),
                'gif' => $imageToBeTransformed->toGif(),
                'webp' => $imageToBeTransformed->toWebp(),
                default => $imageToBeTransformed->toJpeg(),
            };

            // Free up memory
            unset($imageToBeTransformed);

            // Save to replace original image
            Storage::disk('public')->put($this->image->path, $encodedImage);

            // Update status to completed
            $this->image->update(['status' => 'completed']);

            Log::info('Image transformation completed', [
                'uuid' => $this->image->uuid,
                'path' => $this->image->path,
            ]);

            // Check if all images in this upload are done and update upload status
            $this->updateUploadStatus();

            // Fire event (keeping for future WebSocket support)
            event(new ImageTransformed($this->image->uuid, $this->image->path, $this->image->original_filename));
        } catch (Exception $e) {
            Log::error('Image transformation failed', [
                'uuid' => $this->image->uuid,
                'path' => $this->image->path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                // Update status to failed
                $this->image->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                // Check if all images in this upload are done and update upload status
                $this->updateUploadStatus();

                // Fire event (keeping for future WebSocket support)
                event(new ImageTransformationFailed($this->image->uuid, $this->image->path, $this->image->original_filename, $e->getMessage()));
            }

            // Re-throw to mark job as failed (triggers auto-retry by Laravel)
            throw $e;
        }

    }

    /**
     * Update the upload status based on all images' statuses.
     */
    protected function updateUploadStatus(): void
    {
        $upload = $this->image->upload;

        // Get all images for this upload
        $images = $upload->images;

        // Check if all images are done (completed or failed)
        $allDone = $images->every(function ($image) {
            return in_array($image->status, ['completed', 'failed']);
        });

        if ($allDone) {
            // Check if any failed
            $anyFailed = $images->contains(function ($image) {
                return $image->status === 'failed';
            });

            $upload->update([
                'status' => $anyFailed ? 'failed' : 'completed',
            ]);

            Log::info('Upload status updated', [
                'upload_id' => $upload->id,
                'status' => $upload->status,
            ]);
        }
    }
}
