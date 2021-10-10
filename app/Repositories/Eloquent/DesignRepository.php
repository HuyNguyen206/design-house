<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;

class DesignRepository extends BaseRepository implements \App\Repositories\Contracts\DesignInterface
{

    public function model()
    {
        return Design::class;
    }


}
