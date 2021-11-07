<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design, $fileName, $imagePath;

    /**
     * Create a new job instance.
     *
     * @param Design $design
     * @param $fileName
     * @param $imagePath
     */
    public function __construct(Design $design, $fileName, $imagePath)
    {
        $this->design = $design;
        $this->fileName = $fileName;
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        try {
            $originalFile = public_path('storage/' . $this->imagePath);
            Image::make($originalFile)->fit(800, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($large = public_path('storage/uploads/large/') . $this->fileName);

            Image::make($originalFile)->fit(250, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save($thumbnail = public_path('storage/uploads/thumbnail/') . $this->fileName);

            if ($disk == 's3') {
                if (Storage::put('uploads/original/' . $this->fileName, fopen($originalFile, 'r+'))) {
                    File::delete($originalFile);
                }
                if (Storage::put('uploads/large/' . $this->fileName, fopen($large, 'r+'))) {
                    File::delete($large);
                }
                if (Storage::put('uploads/thumbnail/' . $this->fileName, fopen($thumbnail, 'r+'))) {
                    File::delete($thumbnail);
                }
            }

            $this->design->update([
                'image' => $this->imagePath,
                'upload_success' => true
            ]);
        } catch (\Throwable $ex) {
            Log::error($ex->getMessage());
        }
    }
}
