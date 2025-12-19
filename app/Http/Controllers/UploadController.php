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
            'files.*.max' => 'Every image must be less than 10MB',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|uuid',
            'width' => 'required|integer|min:1|max:5000',
            'width.max' => 'Width cannot exceed 5000px',
            'height' => 'required|integer|min:1|max:5000',
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
