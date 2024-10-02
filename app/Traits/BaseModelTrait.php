<?php

namespace App\Traits;

trait BaseModelTrait
{
    public static function getTableName()
    {
        return ((new self)->getTable());
    }
}
