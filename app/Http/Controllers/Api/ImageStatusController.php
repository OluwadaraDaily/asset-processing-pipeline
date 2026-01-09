<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageStatusController extends Controller
{
    public function show(string $uuid): JsonResponse
    {
        $image = Image::where('uuid', $uuid)->first();

        if (! $image) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Image not found',
            ], 404);
        }

        $response = [
            'uuid' => $image->uuid,
            'status' => $image->status,
            'original_filename' => $image->original_filename,
        ];

        if ($image->status === 'completed' && $image->path) {
            $response['url'] = Storage::url($image->path);
            $response['path'] = $image->path;
        }

        if ($image->status === 'failed' && $image->error_message) {
            $response['error_message'] = $image->error_message;
        }

        return response()->json($response);
    }
}
