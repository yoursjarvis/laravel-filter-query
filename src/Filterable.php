<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Filterable
{
    public function scopeFilters(Builder $builder, $filter_terms): Builder
    {
        collect($filter_terms)->map(function ($filter_term) use ($builder) {

            $column = Str::beforeLast($filter_term, ',');
            $value = Str::afterLast($filter_term, ',');

            switch (true) {
                case str_contains($value, '_'):

                    $array_value = explode('_', $value);
                    $builder->whereBetween($column, $array_value);
                    break;

                case str_contains($value, '!'):

                    $array_value = explode('!', $value);
                    $builder->whereNotBetween($column, $array_value);
                    break;

                case str_contains($value, '>'):

                    $builder->where($column, '>', substr($value, 1));
                    break;

                case str_contains($value, '>='):

                    $builder->where($column, '<=', substr($value, 2));
                    break;

                case str_contains($value, '<'):

                    $builder->where($column, '<', substr($value, 1));
                    break;

                case str_contains($value, '<='):
                    $builder->where($column, '<=', substr($value, 2));
                    break;

                case $column === 'trashed' && $value === 'only':

                    $builder->onlyTrashed();
                    break;

                case $column === 'trashed' && $value === 'with':

                    $builder->withTrashed();
                    break;

                default:

                    $builder->where($column, '=', $value);
                    break;
            }
        });

        return $builder;
    }
}
