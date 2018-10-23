<?php
namespace reka\QueryBuilder;

interface Query
{
    public function result();
    public function execute();
}
