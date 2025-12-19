<?php

namespace App\Http\Controllers;

use App\Jobs\TransformImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|image|max:10240',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
            'width' => 'required|integer|min:1|max:5000',
            'height' => 'required|integer|min:1|max:5000',
        ], [
            'files.required' => 'Please select at least one image to upload.',
            'files.*.required' => 'One of the selected files is invalid.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.image' => 'All files must be images.',
            'files.*.max' => 'Each image must be less than 10MB.',
            'uuids.required' => 'Upload identifiers are missing.',
            'uuids.*.uuid' => 'Invalid upload identifier.',
            'width.required' => 'Width is required.',
            'width.integer' => 'Width must be a number.',
            'width.min' => 'Width must be at least 1px.',
            'width.max' => 'Width cannot exceed 5000px.',
            'height.required' => 'Height is required.',
            'height.integer' => 'Height must be a number.',
            'height.min' => 'Height must be at least 1px.',
            'height.max' => 'Height cannot exceed 5000px.',
        ]);

        $files = $request->file('files');
        $uuids = $request->input('uuids');
        $width = $request->input('width');
        $height = $request->input('height');

        foreach ($files as $index => $file) {
            $uuid = $uuids[$index];
            $originalFilename = $file->getClientOriginalName();

            $path = $file->store('images/test', 'public');

            TransformImage::dispatch($uuid, $path, $originalFilename, $width, $height);
        }

        return back()->with('success', 'Files uploaded successfully!');
    }
}
