<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Jobs\UploadImage;
use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\ApplyEagerLoading;
use App\Repositories\Eloquent\Criteria\FilterByWhereField;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\Latest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public $designRepository;

    public function __construct(DesignInterface $designRepository)
    {
        $this->designRepository = $designRepository;
    }

    public function index()
    {
        $designs = $this->designRepository->withCriteria([
            new ApplyEagerLoading('user.designs'),
            new Latest(),
            new FilterByWhereField('is_live', true)
        ])->all();
        return response()->success(DesignResource::collection($designs)->response()->getData());
    }

    public function findDesignById($id)
    {
        return response()->success(new DesignResource($this->designRepository->find($id)));
    }

    //
    public function upload()
    {
        \request()->validate([
            'image' => 'required|mimes:jpeg,gif,bmp,png|max:2048'
        ]);

        $image = \request()->file('image');
        $fileName = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
        $design = $this->designRepository->create([
            'disk' => config('filesystems.default'),
            'user_id' => auth()->id()
        ]);
//        $design = auth()->user()->designs()->create([
//            'disk' => config('filesystems.default')
//        ]);
        $this->dispatch(new UploadImage($design, $fileName, $image));
        return response()->success($design, 'Upload successfully!');
    }

    public function updateDesignInfo($id)
    {
        //        if (auth()->user()->cannot('update', $design)) {
//            return response()->error('You are not the owner of this design');
//        }
        $design = $this->designRepository->find($id);
        $this->authorize('update', $design);
        $data = \request()->validate([
            'title' => 'required|min:3',
            'description' => 'required|min:10',
            'tags' => 'required'
        ]);
        $design->retag($data['tags']);
        unset($data['tags']);
        $data['slug'] = Str::slug($data['title']);
        $data['is_live'] = $design->upload_success;
        $design->update($data);

        return response()->success(new DesignResource($design), 'Update info successfully');

    }

    public function deleteDesign($id)
    {
        $design = $this->designRepository->find($id);
        $this->authorize('delete', $design);
        Storage::disk($design->disk)->delete([
            $design->image,
            Str::replace('original', 'large', $design->image),
            Str::replace('original', 'thumbnail', $design->image),
        ]);
        $design->delete();
        return response()->success([], 'Delete successfully');

    }

}
