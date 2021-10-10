<?php


namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\CriterionInterface;

class Latest implements CriterionInterface
{

    public function apply($model)
    {
       return $model->latest();
    }
}
