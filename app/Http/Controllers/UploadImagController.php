<?php

namespace App\Http\Controllers;




use Illuminate\Http\Request;

use App\Jobs\S3ImageUploadJob;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;

class UploadImagController extends Controller
{
    public function index()
    {
        return view('Images.create');
    }
    public function progress()
    {
        return response()->json([
            'progress' => session('progress', 0),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $images = $request->file('images');
        $uploadedImages = [];
        $totalImages = count($images);
        $uploadedCount = 0;

        foreach ($images as  $key=> $image) {
            $filename = $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename);
            $uploadedImages[] = $filename;

            // Upload image to AWS S3 using queue job
            $job = new S3ImageUploadJob(storage_path('app/' . $path));
            dispatch($job);
            // Increment progress for each image uploaded
            $uploadedCount++;
            $progress = ceil(($uploadedCount / $totalImages) * 100);

            session(['progress' => $progress]);

        return response()->json([
            'progress' => session('progress'),
            'message' => $key+1 . ' Images téléchargées sur bucket S3 avec succès '
        ]);
        }

    }
}
