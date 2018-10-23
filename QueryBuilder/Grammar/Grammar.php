<?php

namespace reka\QueryBuilder\Grammar;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\Helper\Condition\Join;
use reka\QueryBuilder\Helper\Symbol;
use reka\QueryBuilder\Helper\Symbol\Table;
use reka\QueryBuilder\QueryResult;
use reka\QueryBuilder\Query\Delete;
use reka\QueryBuilder\Query\Insert;
use reka\QueryBuilder\Query\Select;
use reka\QueryBuilder\Query\Update;
use reka\Utils\Util;

class Grammar
{
    protected $delete = "DELETE FROM {{table}}{{where}}";
    protected $insert = "INSERT INTO {{table}}{{column}} VALUES({{value}})";
    protected $update = "UPDATE {{table}} SET {{set}}{{where}}";
    protected $select = "SELECT{{distinct}} {{column}} FROM {{table}}{{join}}{{where}}{{group}}{{having}}{{order}}{{limit}}";

    protected $alias = "AS";

    protected $where = "WHERE";

    protected $distinct = "DISTINCT";
    protected $join = "JOIN";
    protected $on = "ON";
    protected $group = "GROUP BY";
    protected $having = "HAVING";
    protected $order = "ORDER BY";
    protected $limit = "LIMIT";

    final public function compileDelete(Delete $delete)
    {
        $table = $delete->getTable();
        $where = $delete->getWhere();

        $cwhere = $this->compCondition($where);

        $comps = array();
        $params = array();

        $comps['table'] = $this->compSymbol($table)->getQuery();

        $comps['where'] = $cwhere->getQuery();

        if (strlen($comps['where']) > 0) {
            $comps['where'] = " " . $this->where . " " . $comps['where'];
        }

        $params = $cwhere->getParam();

        return new QueryResult($this->compileSql($this->delete, $comps), $params);
    }

    final public function compileInsert(Insert $insert)
    {
        $table = $insert->getTable();
        $data = $insert->getData();

        $comps = array();
        $params = array();

        $comps['table'] = $this->compSymbol($table)->getQuery();

        $comps['column'] = $this->compColumn($table)->getQuery();

        $comps['value'] = implode(", ", array_fill(0, count($data), "?"));

        if (strlen($comps['column']) > 0) {
            $comps['column'] = "(" . $comps['column'] . ")";
        }

        $params = $data;

        return new QueryResult($this->compileSql($this->insert, $comps), $params);
    }

    final public function compileUpdate(Update $update)
    {
        $table = $update->getTable();
        $data = $update->getData();
        $where = $update->getWhere();

        $cwhere = $this->compCondition($where);

        $comps = array();
        $params = array();

        $comps['table'] = $this->compSymbol($table)->getQuery();

        $comps['set'] = $this->compColumn($table, "=?")->getQuery();

        $comps['where'] = $cwhere->getQuery();

        if (strlen($comps['where']) > 0) {
            $comps['where'] = " " . $this->where . " " . $comps['where'];
        }

        $params = array_merge($data, $cwhere->getParam());

        return new QueryResult($this->compileSql($this->update, $comps), $params);
    }
    // distinct, column, table, join, where, group, having, order
    public function compileSelect(Select $select)
    {
        $table = $select->getTable();
        $distinct = $select->getDistinct();
        $join = $select->getJoin();
        $where = $select->getWhere();
        $group = $select->getGroup();
        $having = $select->getHaving();
        $order = $select->getOrder();
        $limit = $select->getLimit();

        $ctable = array_map([$this, "compSymbol"], $table->toArray());
        $ccolumn = array_map([$this, "compColumn"], $table->toArray());
        $cwhere = $this->compCondition($where);
        $cjoin = array_map([$this, "compJoin"], $join->toArray());
        $cgroup = array_map([$this, "compSymbol"], $group->toArray());
        $chaving = $this->compCondition($having);
        $corder = array_map(function ($order) {return is_array($order) ? $this->compSymbol($order[0]) . " " . $order[1] : $this->compSymbol($order) . " ASC";}, $order);
        $climit = is_null($limit) ? "" : $limit[1] > 0 ? implode(", ", $limit) : $limit[0];

        $comps = array();
        $params = array();

        $ccolumn = array_map(function ($column) {return $column->getQuery();}, $ccolumn);

        $comps['column'] = implode(", ", $ccolumn);

        $comps['table'] = implode(", ", array_map(function ($table) {return $table->getQuery();}, $ctable));

        $comps['distinct'] = $distinct ? " DISTINCT" : "";

        $comps['join'] = implode(" ", array_map(function ($join) {return $join->getQuery();}, $cjoin));

        $comps['where'] = $cwhere->getQuery();

        $comps['group'] = implode(", ", $cgroup);

        $comps['having'] = $chaving->getQuery();

        $comps['order'] = implode(", ", $corder);

        $comps['limit'] = $climit;

        if (strlen($comps['join']) > 0) {
            $comps['join'] = " " . $this->join . " " . $comps['join'];
        }

        if (strlen($comps['where']) > 0) {
            $comps['where'] = " " . $this->where . " " . $comps['where'];
        }

        if (strlen($comps['group']) > 0) {
            $comps['group'] = " " . $this->group . " " . $comps['group'];
        }

        if (strlen($comps['having']) > 0) {
            $comps['having'] = " " . $this->having . " " . $comps['having'];
        }

        if (strlen($comps['order']) > 0) {
            $comps['order'] = " " . $this->order . " " . $comps['order'];
        }

        if (strlen($comps['limit']) > 0) {
            $comps['limit'] = " " . $this->limit . " " . $comps['limit'];
        }

        $params = array_merge($params, ...array_map(function ($table) {return $table->getParam();}, $ctable));
        $params = array_merge($params, $cwhere->getParam());
        $params = array_merge($params, $chaving->getParam());

        return new QueryResult($this->compileSql($this->select, $comps), $params);
    }

    private function compColumn(Table $table, string $option = "")
    {
        $select = $table->getSelect();

        $query = array_map(function ($column) use ($table, $option, $select) {
            return $this->quote(is_null($select) ? $table->getName() : $table->getAlias()) . "." . $this->compSymbol($column)->getQuery() . $option;
        }, $table->getColumn()->toArray());

        $param = array();

        if (!is_null($select)) {
            $param = $select->getParam();
        }

        return new QueryResult(implode(", ", $query), $param);
    }

    private function compSymbol(Symbol $symbol)
    {
        $name = $symbol->getName();
        $alias = $symbol->getAlias();

        if ($symbol instanceof Table) {
            $select = $symbol->getSelect();

            if (!is_null($select)) {
                return new QueryResult($name . " " . $this->alias . " '" . $alias . "'", $select->getParam());
            }
        }

        $name = $this->quote($name);

        if (is_null($alias)) {
            return new QueryResult($name, []);
        } else {
            return new QueryResult($name . " " . $this->alias . " '" . $alias . "'", []);
        }
    }

    private function isCondition($cond): bool
    {
        return is_array($cond) && count($cond) >= 3 && is_string($cond[0]);
    }

    private function compCondition($condition, $params = true)
    {
        $condition = Util::cleanArgs($condition);
        $check = $this->isCondition($condition);

        $query = "";
        $data = array();

        if ($check) {
            $query = Builder::grammar()->quote($condition[0]) . $condition[1];
            if ($params) {
                $query .= "?";
                array_push($data, $condition[2]);
            } else {
                $query .= Builder::grammar()->quote($condition[2]);
            }
        } elseif (!is_null($condition)) {
            foreach ($condition as $cond) {
                if (is_array($cond)) {
                    $r = $this->compCondition($cond);
                    $query .= "(" . $r->getQuery() . ")";
                    $data = array_merge($data, $r->getParam());
                } else if (is_string($cond)) {
                    $query .= " " . trim($cond) . " ";
                }
            }
        }

        return new QueryResult($query, $data);
    }

    private function compJoin(Join $join)
    {
        $type = $join->getType();
        $table = $this->compSymbol($join->getTable());
        $on = $this->compCondition($join->getOn(), false);

        $query = "";

        if ($type !== "") {
            $query .= $type . " ";
        }

        $query .= "JOIN " . $table;

        if (strlen($on->getQuery()) > 0) {
            $query .= " " . $this->on . " " . $on->getQuery();
        }

        return new QueryResult($query, []);
    }

    private function compileSql(string $sqlbase, array $args)
    {
        $sql = $sqlbase;

        foreach ($args as $key => $value) {
            $sql = str_replace("{{" . $key . "}}", $value, $sql);
        }

        return trim($sql);
    }

    public function quote(string $name): string
    {
        if ($name === "*") {
            return $name;
        }

        if (strpos($name, ".") > 0) {
            return implode(".", array_map([$this, "quote"], explode(".", $name)));
        } else {
            return "`" . $name . "`";
        }
    }
}
