<?php

namespace App\Http\Controllers;

use App\Jobs\TransformImage;
use App\Models\Image;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|image|max:10240',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
            'widths' => 'required|array|min:1',
            'widths.*' => 'required|integer|min:1|max:10000',
            'heights' => 'required|array|min:1',
            'heights.*' => 'required|integer|min:1|max:10000',
            'targetWidths' => 'required|array|min:1',
            'targetWidths.*' => 'required|integer|min:1|max:10000',
            'targetHeights' => 'required|array|min:1',
            'targetHeights.*' => 'required|integer|min:1|max:10000',
        ], [
            'files.required' => 'Please select at least one image to upload.',
            'files.*.required' => 'One of the selected files is invalid.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.image' => 'All files must be images.',
            'files.*.max' => 'Each image must be less than 10MB.',
            'uuids.required' => 'Upload identifiers are missing.',
            'uuids.*.uuid' => 'Invalid upload identifier.',
            'widths.required' => 'Original widths are required.',
            'widths.*.integer' => 'Width must be a number.',
            'widths.*.min' => 'Width must be at least 1px.',
            'widths.*.max' => 'Width cannot exceed 10000px.',
            'heights.required' => 'Original heights are required.',
            'heights.*.integer' => 'Height must be a number.',
            'heights.*.min' => 'Height must be at least 1px.',
            'heights.*.max' => 'Height cannot exceed 10000px.',
            'targetWidths.required' => 'Target widths are required.',
            'targetWidths.*.integer' => 'Target width must be a number.',
            'targetWidths.*.min' => 'Target width must be at least 1px.',
            'targetWidths.*.max' => 'Target width cannot exceed 10000px.',
            'targetHeights.required' => 'Target heights are required.',
            'targetHeights.*.integer' => 'Target height must be a number.',
            'targetHeights.*.min' => 'Target height must be at least 1px.',
            'targetHeights.*.max' => 'Target height cannot exceed 10000px.',
        ]);

        // Additional validation: ensure all arrays have the same length
        $filesCount = count($request->file('files'));
        $uuidsCount = count($request->input('uuids'));
        $widthsCount = count($request->input('widths'));
        $heightsCount = count($request->input('heights'));
        $targetWidthsCount = count($request->input('targetWidths'));
        $targetHeightsCount = count($request->input('targetHeights'));

        if ($filesCount !== $uuidsCount || $filesCount !== $widthsCount || $filesCount !== $heightsCount || $filesCount !== $targetWidthsCount || $filesCount !== $targetHeightsCount) {
            return back()->withErrors(['files' => 'Mismatched data arrays. Please try again.']);
        }

        $files = $request->file('files');
        $uuids = $request->input('uuids');
        $widths = $request->input('widths');
        $heights = $request->input('heights');
        $targetWidths = $request->input('targetWidths');
        $targetHeights = $request->input('targetHeights');

        // Get or create session ID for guest tracking
        $sessionId = session()->getId();
        if (! $sessionId) {
            session()->start();
            $sessionId = session()->getId();
        }

        DB::transaction(function () use ($request, $files, $uuids, $widths, $heights, $targetWidths, $targetHeights, $sessionId) {
            // Create Upload record
            $upload = Upload::create([
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'device' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'no_of_files' => count($files),
                'status' => 'processing',
                'storage_path' => 'images/'.date('Y-m-d'),
            ]);

            foreach ($files as $index => $file) {
                $uuid = $uuids[$index]; // Use frontend-provided UUID
                $originalFilename = $file->getClientOriginalName();
                $targetWidth = $targetWidths[$index];
                $targetHeight = $targetHeights[$index];

                // Store file
                $path = $file->store('images/'.date('Y-m-d'), 'public');

                // Create Image record with frontend-provided UUID
                $image = Image::create([
                    'upload_id' => $upload->id,
                    'uuid' => $uuid, // Use the UUID from frontend
                    'original_filename' => $originalFilename,
                    'path' => $path,
                    'status' => 'pending',
                    'original_width' => $widths[$index],
                    'original_height' => $heights[$index],
                    'target_width' => $targetWidth,
                    'target_height' => $targetHeight,
                ]);

                // Dispatch job
                TransformImage::dispatch($image);
            }
        });

        return back()->with('success', 'Files uploaded successfully!');
    }
}
