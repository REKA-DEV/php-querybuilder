<?php
namespace reka\QueryBuilder\Helper\Multiplier;

use reka\QueryBuilder\Helper\Condition\Join;
use reka\QueryBuilder\Helper\Multiplier;
use reka\QueryBuilder\Query;

class Joins extends Multiplier
{
    private $query = null;

    public function __construct(Query $query = null)
    {
        parent::__construct();

        $this->query = $query;
    }

    public function add($table, $type = "")
    {
        $data = new Join($table, $type, $this->query);
        $this->push($data);

        return $data;
    }

}
