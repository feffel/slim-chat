<?php

namespace Chat\Validation\Rules;

use Illuminate\Database\Query\Builder;
use Respect\Validation\Rules\AbstractRule;

class ExistsInTable extends AbstractRule
{
    private string $column;

    private Builder $table;

    public function __construct($table, $column)
    {
        $this->table  = $table;
        $this->column = $column;
    }


    public function validate($input)
    {
        return $this->table->where($this->column, $input)->exists();
    }
}
