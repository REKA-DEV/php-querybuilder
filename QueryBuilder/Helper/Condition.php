<?php
namespace reka\QueryBuilder\Helper;

use reka\QueryBuilder\Builder;
use reka\QueryBuilder\QueryResult;
use reka\Utils\Util;

class Condition
{
    private $cond = null;

    private $result = null;

    public function __construct()
    {
        $this->cond = Util::cleanArgs(func_get_args());

        $this->result = $this->execute();
    }

    private function execute($now = null)
    {
        if (is_null($now)) {
            $now = $this->cond;
        }

        $check = $this->check($now);

        $query = "";
        $data = array();

        if ($check) {
            $query = Builder::grammar()->quote($now[0]) . $now[1] . "?";
            array_push($data, $now[2]);
        } else {
            foreach ($now as $n) {
                if (is_array($n)) {
                    $r = $this->execute($n);
                    $query .= "(" . $r->getQuery() . ")";
                    $data = array_merge($data, $r->getData());
                } else if (is_string($n)) {
                    $query .= " " . trim($n) . " ";
                }
            }
        }

        return new QueryResult($query, $data);
    }

    public function getResult(): ?QueryResult
    {
        return $this->result;
    }

    private function check(array $cond): bool
    {
        return is_array($cond) && count($cond) >= 3 && is_string($cond[0]);
    }
}
