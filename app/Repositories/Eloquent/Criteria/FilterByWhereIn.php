<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class FilterByWhereIn implements CriterionInterface
{
    private $column, $value;

    /**
     * FilterByWhereField constructor.
     * @param $column
     * @param $value
     */
    public function __construct($column, array $value)
    {
        $this->column = $column;
        $this->value = $value;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function apply($model)
    {
       return $model->whereIn($this->column, $this->value);
    }
}
