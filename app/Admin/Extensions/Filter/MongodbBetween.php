<?php

namespace App\Admin\Extensions\Filter;

use Encore\Admin\Grid\Filter\Between;

class MongodbBetween extends Between
{
    public function condition($input)
    {
        if (array_has($input, $this->column)) {
            $this->value = array_values($input[$this->column]);
        }

        return $this->buildCondition($this->column, $this->value);
    }
}