<?php
namespace reka\QueryBuilder\Helper\Condition;

use reka\Utils\Util;

class Whereable
{
    private $where = null;

    public function where(): self
    {
        if (is_null($this->where)) {
            $this->where = array();
            array_push($this->where, Util::cleanArgs(func_get_args()));
        } else {
            $this->andWhere(func_get_args());
        }

        return $this;
    }

    public function andWhere(): self
    {
        array_push($this->where, "AND");
        array_push($this->where, Util::cleanArgs(func_get_args()));

        return $this;
    }

    public function orWhere(): self
    {
        array_push($this->where, "OR");
        array_push($this->where, Util::cleanArgs(func_get_args()));

        return $this;
    }

    public function getWhere():  ? array
    {
        return $this->where;
    }
}
