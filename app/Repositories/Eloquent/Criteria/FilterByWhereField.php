<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class FilterByWhereField implements CriterionInterface
{
    private $column, $value;

    /**
     * FilterByWhereField constructor.
     * @param $column
     * @param $value
     */
    public function __construct($column, $value)
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
       return $model->where($this->column, $this->value);
    }
}
