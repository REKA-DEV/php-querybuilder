<?php
namespace reka\QueryBuilder\Helper\Condition;

use reka\QueryBuilder\Helper\Symbol\Table;
use reka\QueryBuilder\Query;
use reka\Utils\Util;

class Join
{
    private $table = null;
    private $type = null;

    private $cond = null;

    private $query = null;

    public function __construct($table, $type = "", Query $query = null)
    {
        $this->table = new Table($table);

        $this->query = $query;
        $this->type = $type;
    }

    public function on():  ? Query
    {
        $this->cond = Util::cleanArgs(func_get_args());

        if (!is_null($this->query)) {
            return $this->query;
        }
        return null;
    }

    public function all()
    {
        return $this->query;
    }

    public function getTable() : Table
    {
        return $this->table;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOn():  ? array
    {
        return $this->cond;
    }
}
