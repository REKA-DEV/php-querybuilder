<?php
namespace reka\QueryBuilder\Helper;

class Symbol
{
    private $name = null;
    private $alias = null;

    public function set($set)
    {
        if (is_string($set)) {
            $this->name($set);
        } elseif (is_array($set) && count($set) === 2) {
            $this->name($set[0]);
            $this->alias($set[1]);
        } else {
            // exception
        }
    }

    public function name(string $name)
    {
        $this->name = $name;
    }

    public function alias(string $alias)
    {
        $this->alias = $alias;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}
