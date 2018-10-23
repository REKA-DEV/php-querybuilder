<?php
namespace reka\QueryBuilder\Query;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\Helper\Condition\Whereable;
use reka\QueryBuilder\Helper\Symbol\Table;
use reka\QueryBuilder\Query;
use reka\Utils\Util;

class Update extends Whereable implements Query
{
    private $table = null;
    private $data = null;

    public function table(string $table): Table
    {
        $this->table = new Table($table, $this);
        return $this->table;
    }

    public function data(): self
    {
        $this->data = Util::cleanArgs(func_get_args());

        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function result()
    {
        $result = Builder::grammar()->compileUpdate($this);

        return $result;
    }

    public function execute(): int
    {
        $result = $this->result();

        return DB::execute($result->sql, $result->data);
    }
}
