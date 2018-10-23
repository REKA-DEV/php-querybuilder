<?php

namespace reka\QueryBuilder\Helper\Symbol;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\Helper\Multiplier\Columns;
use reka\QueryBuilder\Helper\Symbol;
use reka\QueryBuilder\Query;
use reka\QueryBuilder\Query\Select;
use reka\Utils\Util;

class Table extends Symbol
{
    private $select = null;
    private $column = null;

    private $query = null;

    public function __construct($table, Query $query = null)
    {
        if (is_array($table) && $table[0] instanceof Select) {
            $this->select = Builder::grammar()->compileSelect($table[0]);
            $this->set(["(" . $this->select->getQuery() . ")", $table[1]]);
        } else if (is_string($table) || is_array($table)) {
            $this->set($table);
        } else {
            // exception : cant be table
        }

        $this->column = new Columns();
        $this->query = $query;
    }

    public function singleColumn()
    {
        $column = Util::cleanArgs(func_get_args());
        $this->column->add($column);

        if (!is_null($this->query)) {
            return $this->query;
        }
    }

    public function column()
    {
        $columns = Util::cleanArgs(func_get_args());

        if (count($columns) === 0) {
            $columns = ["*"];
        }

        foreach ($columns as $column) {
            $this->column->add($column);
        }

        if (!is_null($this->query)) {
            return $this->query;
        }
    }

    public function getSelect()
    {
        return $this->select;
    }

    public function getColumn()
    {
        return $this->column;
    }
}
