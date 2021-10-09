<?php


namespace App\Repositories\Eloquent;


use App\Models\User;

class UserRepository extends BaseRepository implements \App\Repositories\Contracts\UserInterface
{

    public function model()
    {
       return User::class;
    }

}
