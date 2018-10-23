<?php

namespace reka\QueryBuilder\Helper\Symbol;

use reka\QueryBuilder\Helper\Symbol;

class Column extends Symbol
{
    private $name = null;
    private $alias = null;

    public function __construct($column)
    {
        $this->set($column);
    }
}
