<?php

namespace App;

class MySqlQueryBuilder implements QueryBuilderInterface
{
    private array $fields = [];
    private string $table = '';
    private array $conditions = [];

    public function select(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function getSQL(): string
    {
        $sql = "SELECT ";
        $sql .= empty($this->fields) ? "*" : implode(', ', $this->fields);
        
        $sql .= " FROM " . $this->table;
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }
        $sql .= ";";
        
        return $sql;
    }
}