<?php
namespace reka\QueryBuilder\Query;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\Helper\Condition\Whereable;
use reka\QueryBuilder\Helper\Symbol\Table;
use reka\QueryBuilder\Query;

class Delete extends Whereable implements Query
{
    private $table = null;

    public function table(string $name): self
    {
        $this->table = new Table($name);

        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function result()
    {
        // exception : no table

        $result = Builder::grammar()->compileDelete($this);

        return $result;
    }

    public function execute()
    {
        $result = $this->result();

        return DB::execute($result->sql, $result->data);
    }
}
