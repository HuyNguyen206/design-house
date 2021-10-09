<?php


namespace App\Repositories\Eloquent;


use App\Exceptions\ModelMethodNotDefine;

abstract class BaseRepository implements \App\Repositories\Contracts\BaseInterface
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
        $this->model->all();
        // TODO: Implement all() method.
    }

    public function paginate()
    {
       return  $this->model->paginate(20);
        // TODO: Implement paginate() method.
    }
}
