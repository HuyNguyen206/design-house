<?php
namespace App\Repositories\Contracts;

interface BaseInterface
{
    public function all();

    public function paginate();
}
