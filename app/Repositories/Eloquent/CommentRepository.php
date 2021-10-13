<?php


namespace App\Repositories\Eloquent;


use App\Models\Comment;

class CommentRepository extends BaseRepository implements \App\Repositories\Contracts\CommentInterface
{

    public function model()
    {
       return Comment::class;
    }

    /**
     * @return mixed
     */
    public function deleteByFilter()
    {
       $this->model->delete();
    }
}
