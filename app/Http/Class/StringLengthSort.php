<?php

namespace App\Http\Class;

use Illuminate\Database\Eloquent\Builder;

class StringLengthSort implements \Spatie\QueryBuilder\Sorts\Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->orderByRaw("LENGTH(`{$property}`)  {$direction}, `{$property}` {$direction}");
    }
}
