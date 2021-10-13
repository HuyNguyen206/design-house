<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ApplyEagerLoading implements CriterionInterface
{
    private $relationship;

    /**
     * FilterByWhereField constructor.
     * @param array $relationship
     */
    public function __construct(array $relationship)
    {
        $this->relationship = $relationship;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function apply($model)
    {
        foreach ($this->relationship as $relation) {
            $model = $model->with($relation);
        }
       return $model;
    }
}
