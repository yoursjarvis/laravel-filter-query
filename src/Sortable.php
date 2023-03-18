<?php

namespace YoursJarvis\FiltersQuery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Sortable
{
    public function scopeSortBy(Builder $builder, string $sort_terms = null): Builder
    {
        if ($sort_terms) {

            collect($sort_terms)->map(function ($sort_term) use ($builder) {
                $column = Str::beforeLast($sort_term, ',');
                $direction = Str::afterLast($sort_term, ',');

                $builder->orderBy($column, $direction);
            });
        } else {
            $builder->latest();
        }

        return $builder;
    }
}
