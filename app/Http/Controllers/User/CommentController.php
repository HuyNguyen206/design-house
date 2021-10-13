<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Design;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public  $designRepo, $commentRepo;
    public function __construct(DesignInterface $designRepo, CommentInterface $commentRepo)
    {
        $this->designRepo = $designRepo;
        $this->commentRepo = $commentRepo;
    }

    public function createComment()
    {
        $data = \request()->validate([
           'comment' => 'required|min:5',
            'design_id' => 'required'
        ]);
        $design = $this->designRepo->find($data['design_id']);

        unset($data['design_id']);
        $data['user_id'] = auth()->id();
        $comment = $design->comments()->create($data);
        return response()->success(new CommentResource($comment));
    }

    public function updateComment($id)
    {
        $comment = $this->commentRepo->find($id);
        $this->authorize('update', $comment);
        $data = \request()->validate([
           'comment' =>  'required'
        ]);
        $comment->update($data);
        return response()->success(new CommentResource($comment));
    }

    public function deleteComment($id)
    {
        $comment = $this->commentRepo->find($id);
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->success([]);
    }
}
