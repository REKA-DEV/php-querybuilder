<?php
namespace reka\QueryBuilder\Query;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\Helper\Condition\Whereable;
use reka\QueryBuilder\Helper\Multiplier\Joins;
use reka\QueryBuilder\Helper\Multiplier\Tables;
use reka\QueryBuilder\Helper\Symbol\Column;
use reka\QueryBuilder\Query;
use reka\Utils\Util;

class Select extends Whereable implements Query
{
    private $table = null;
    private $distinct = false;
    private $join = null;
    private $group = null;
    private $having = null;
    private $order = array();
    private $limit = null;

    public function __construct()
    {
        $this->table = new Tables($this);
        $this->join = new Joins($this);
        $this->group = new Tables($this);
    }

    public function table($table)
    {
        return $this->table->add($table);
    }

    public function distinct(): self
    {
        $this->distinct = true;

        return $this;
    }

    public function join($table, $type = "")
    {
        return $this->join->add($table, $type);
    }

    public function leftJoin($table)
    {
        return $this->join($table, "LEFT");
    }

    public function rightJoin($table)
    {
        return $this->join($table, "RIGHT");
    }

    public function fullJoin($table)
    {
        return $this->join($table, "FULL");
    }

    public function group(): self
    {
        foreach (Util::cleanArgs(func_get_args()) as $table) {
            $this->group->add($table);
        }

        return $this;
    }

    public function having(): self
    {
        $this->having = Util::cleanArgs(func_get_args());

        return $this;
    }

    public function singleOrder($target, $option)
    {
        $this->order = array();
        array_push($this->order, [new Column($target), $option]);

        return $this;
    }
    public function order(): self
    {
        $this->order = Util::cleanArgs(array_map(function ($order) {return is_array($order) ? [new Column($order[0]), $order[1]] : new Column($order);}, func_get_args()));

        return $this;
    }

    public function limit(int $offset, int $length = 0): self
    {
        $this->limit = [$offset, $length];

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getDistinct()
    {
        return $this->distinct;
    }

    public function getJoin()
    {
        return $this->join;
    }
    public function getGroup()
    {
        return $this->group;
    }
    public function getHaving()
    {
        return $this->having;
    }
    public function getOrder()
    {
        return $this->order;
    }
    public function getLimit()
    {
        return $this->limit;
    }

    public function result()
    {
        $result = Builder::grammar()->compileSelect($this);

        return $result;
    }

    public function execute()
    {
        $compile = DB::grammar()->compile($this->result);

        return DB::fetchAll($compile->sql, $compile->data);
    }
}
