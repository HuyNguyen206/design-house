<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    //
    public function upload()
    {
        \request()->validate([
            'image' => 'required|mimes:jpeg,gif,bmp,png|max:2048'
        ]);

        $image = \request()->file('image');
        $imagePath = $image->getPathname();
        $fileName = time()."_".preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
//        $tmp = $image->storeAs('uploads/original', $fileName, 'tmp');

        $design = auth()->user()->designs()->create([
//            'image' => $tmp,
            'disk' => config('filesystems.default')
        ]);
        $this->dispatch(new UploadImage($design, $fileName, $image));
        return response()->success($design, 'Upload successfully!');
    }
}
