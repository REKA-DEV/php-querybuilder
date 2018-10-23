<?php
namespace reka\QueryBuilder;

use reka\QueryBuilder\Grammar\MySqlGrammar;
use reka\QueryBuilder\Query\Delete;
use reka\QueryBuilder\Query\Insert;
use reka\QueryBuilder\Query\Select;
use reka\QueryBuilder\Query\Update;

class Builder
{
    private static $grammar = null;

    public function insert(): Insert
    {
        return new Insert();
    }

    public function update(): Update
    {
        return new Update();
    }

    public function delete(): Delete
    {
        return new Delete();
    }

    public function select(): Select
    {
        return new Select();
    }

    public static function grammar()
    {
        if (is_null(self::$grammar)) {
            self::$grammar = new MySqlGrammar();
        }

        return self::$grammar;
    }
}
