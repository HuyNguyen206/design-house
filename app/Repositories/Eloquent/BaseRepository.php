<?php


namespace App\Repositories\Eloquent;


use App\Exceptions\ModelMethodNotDefine;
use App\Models\Design;
use App\Repositories\Criteria\CriteriaInterface;
use Illuminate\Support\Arr;

abstract class BaseRepository implements \App\Repositories\Contracts\BaseInterface, CriteriaInterface
{
    public $model;

    public function __construct()
    {
        $this->makeModel();
    }


    public function makeModel()
    {
        if (!method_exists($this, 'model')) {
            throw new ModelMethodNotDefine('No model define');
        }
        $this->model = app()->make($this->model());
    }

    public function all()
    {
        return $this->model->get();
        // TODO: Implement all() method.
    }

    public function paginate($perPage = 10)
    {
       return  $this->model->paginate($perPage);
        // TODO: Implement paginate() method.
    }
    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function first()
    {
        return $this->model->firstOrFail();
    }

    public function findWhere($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereFirst($column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function create(array $data)
    {
       return $this->model->create($data);
    }
    public function update(int $id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);
        return $model;
    }
    public function delete(int $id)
    {
         $this->find($id)->delete();
    }

    public function withCriteria(...$criteria)
    {
        $criteria = Arr::flatten($criteria);
        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }
        return $this;
    }

    public function search(array $arg) {

    }
}
