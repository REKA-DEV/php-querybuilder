<?php
namespace reka\QueryBuilder\Helper;

abstract class Multiplier
{
    private $data = null;

    public function __construct()
    {
        $this->data = array();
    }

    public function toArray(): array
    {
        return $this->data;
    }

    protected function push($item)
    {
        array_push($this->data, $item);
    }

    abstract public function add($item);
}
