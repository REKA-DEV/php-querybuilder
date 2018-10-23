<?php
namespace reka\QueryBuilder\Query;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\Helper\Symbol\Table;
use reka\QueryBuilder\Query;
use reka\Utils\Util;

class Insert implements Query
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
        // exception : no table
        // exception : no value

        $result = Builder::grammar()->compileInsert($this);

        return $result;
    }

    public function execute(): int
    {
        $result = $this->result();
        DB::execute($result->sql, $result->data);

        return DB::lastId();
    }
}
