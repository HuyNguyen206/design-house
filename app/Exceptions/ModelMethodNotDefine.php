<?php

namespace App\Exceptions;

use Exception;

class ModelMethodNotDefine extends Exception
{

    public function render()
    {
        return response()->error($this->getMessage());
    }
}
