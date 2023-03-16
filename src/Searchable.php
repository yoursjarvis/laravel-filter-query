<?php

namespace YoursJarvis\FiltersQuery;

use Exception;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Searchable
{
    public function scopeSearch(Builder $builder, $search_term): Builder
    {
        if (!$searchable = $this->searchable) {
            throw new Exception("Please define the searchable property on model. ");
        }

        foreach ($searchable as $column) {
            if (str_contains($column, '.')) {

                $relation = Str::beforeLast($column, '.');
                $column = Str::afterLast($column, '.');
                $builder->orWhereRelation($relation, $column, 'like', "%$search_term%");

                continue;
            }

            $builder->orWhere($column, 'like', "%$search_term%");
        }

        return $builder;
    }
}
