<?php
namespace reka\QueryBuilder\Helper\Multiplier;

use reka\QueryBuilder\Helper\Multiplier;
use reka\QueryBuilder\Helper\Symbol\Table;
use reka\QueryBuilder\Query;

class Tables extends Multiplier
{
    private $query = null;

    public function __construct(Query $query = null)
    {
        parent::__construct();

        $this->query = $query;
    }

    public function add($table)
    {
        $data = new Table($table, $this->query);
        $this->push($data);

        return $data;
    }

}
