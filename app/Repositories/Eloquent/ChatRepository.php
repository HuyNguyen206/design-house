<?php


namespace App\Repositories\Eloquent;


use App\Models\Chat;

class ChatRepository extends BaseRepository implements \App\Repositories\Contracts\ChatInterface
{

    public function model()
    {
       return Chat::class;
    }

}
