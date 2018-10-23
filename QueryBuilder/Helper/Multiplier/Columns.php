<?php
namespace reka\QueryBuilder\Helper\Multiplier;

use reka\QueryBuilder\Helper\Multiplier;
use reka\QueryBuilder\Helper\Symbol\Column;

class Columns extends Multiplier
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($column)
    {
        $data = new Column($column);
        $this->push($data);

        return $data;
    }
}
