<?php


namespace App\Repositories\Eloquent;


use App\Models\Team;
use App\Models\User;

class TeamRepository extends BaseRepository implements \App\Repositories\Contracts\TeamInterface
{

    public function model()
    {
       return Team::class;
    }

}
