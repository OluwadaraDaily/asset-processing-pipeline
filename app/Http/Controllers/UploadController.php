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
        ]);

        $files = $request->file('files');
        $allPaths = [];

        foreach ($files as $file) {
            // dd("FILE =>", $file);
            // Process your file here
            $path = $file->store('images/test', 'public');
            $allPaths[] = $path;
            TransformImage::dispatch($path, 100, 100);
        }

        return back()->with('success', 'Files uploaded successfully!');
    }
}
