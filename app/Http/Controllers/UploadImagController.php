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

        $total = count($images);
        $progress = 0;

        foreach ($images as $image) {
            $filename = $image->getClientOriginalName();
            $path = $image->storeAs('images', $filename);

            S3ImageUploadJob::dispatch(storage_path('app/' . $path));

            $progress++;
            session(['progress' => intval($progress / $total * 100)]);
        }

        return response()->json([
            'progress' => session('progress'),

            'message' => $total. 'Images téléchargées sur bucket S3 avec succès '
        ]);
    }
}
