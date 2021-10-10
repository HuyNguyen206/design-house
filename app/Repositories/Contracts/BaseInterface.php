<?php
namespace App\Repositories\Contracts;

interface BaseInterface
{
    public function all();
    public function paginate($perPage = 10);
    public function find(int $id);
    public function findWhere($column, $value);
    public function findWhereFirst($column, $value);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);

}
