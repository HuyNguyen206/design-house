<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ApplyEagerLoading implements CriterionInterface
{
    private $relationship;

    /**
     * FilterByWhereField constructor.
     * @param $column
     * @param $value
     */
    public function __construct($relationship)
    {
        $this->relationship = $relationship;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function apply($model)
    {
       return $model->with($this->relationship);
    }
}
