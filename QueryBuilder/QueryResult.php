<?php
namespace reka\QueryBuilder;

class QueryResult
{
    private $query = null;
    private $param = null;

    public function __construct(string $query, array $param)
    {
        $this->query = $query;
        $this->param = $param;
    }

    public function getQuery():  ?string
    {
        return $this->query;
    }

    public function getParam(): ?array
    {
        return $this->param;
    }
}
