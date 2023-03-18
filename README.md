<center>
<h1>Laravel Filter Query</h1> 
</center>

<img src="https://i.imgur.com/gOBBa0X.png" alt="banner_png">

# Install

```bash
composer require yoursjarvis/laravel-filter-query
```

---

# Searchable Trait

This is a Laravel 9 trait that adds a `scopeSearch()` method to the model that uses it. The `scopeSearch()` method allows you to search the database records based on the term provided.

## How to Use

1. Add the Searchable trait to your model:

```php
use YoursJarvis\FiltersQueryString\Searchable;

class MyModel extends Model
{
  use Searchable;

  protected $searchable = ['column_name_1', 'column_name_2'];
}

```

2.Use the `scopeSearch()` method in your queries:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Test;
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function index(Request $request)
    {
      if ($request->has('q')) {
        $data = MyModel::search($request->q);
      }

        $data->filters($request->filters);
        return response()->json(['data' => $data->get()], 200);
    }
}
```

This will return all records that contain the search term example in either the `column_name_1` or `column_name_2` columns.

## Query Convention

```php
public_url/index?q=value_that_you_want_search
```

## Method Details

`scopeFilter(Builder $builder, array $search_terms = []): Builder`
The `scopeSearch()` method is responsible for searching the database records based on the provided search term. The method accepts two parameters:

- **$builder** - an instance of Illuminate\Database\Eloquent\Builder representing the current query builder instance.
- **$search_terms** - an associative array containing the search term to be used. The array should have a key of search_term and a value of the search term.

The method returns an instance of Illuminate\Database\Eloquent\Builder representing the modified query builder instance.

The `scopeSearch()` method loops through each column defined in the $searchable property and adds a where or orWhere clause to the query builder instance. If the filter contains a dot (.), it assumes that the filter is a relationship and uses the orWhereRelation() method to search the related table. Otherwise, it searches the current table.

## Conclusion

The Searchable trait is a useful addition to any Laravel model that needs to search for specific records based on a search term. It provides a simple and flexible way to filter the database records based on any number of columns, and can be easily customized to fit your specific needs.

---

# Filterable Trait

The Filterable trait is a Laravel trait that adds a `scopeFilters()` method to the model that uses it. The `scopeFilters()` method allows you to filter the database records based on the terms provided.

## How to Use

1. Add the Filterable trait to your model:

```php
use Filterable;

class MyModel extends Model
{
    use Filterable;
}
```

2. Use the `scopeFilters()` method in your controller method:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Test;
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function index(Request $request)
    {
      if ($request->has('filters')) {
        $data = MyModel::filters($request->q);
      }

      return response()->json(['data' => $data->get()], 200);
    }
}
```

This will return all records that match the filter criteria provided in the filter query.

## Query convention

You just need to pass filterable field and value in your query using following convention

```json
public_url/index?filters[0]=field,value
public_url/index?filters[0]=is_active,0
```

Above mention query transform into a eloquent query like

```php
MyModal::query()->where($column, '=', $value);
```

For others query you follow the convention mentioned below:

```json
between: public_url/index?filters[0]=field,value_value
not between: public_url/index?filters[0]=field,value!value
greater: public_url/index?filters[0]=field,>value
greater_or_equal: public_url/index?filters[0]=field,>=value
less: public_url/index?filters[0]field,<=value
less_or_equal: public_url/index?filters[0]field,<=value
with_trashed: public_url/index?filters[0] = trashed,only,
only_trashed: public_url/index?filters[0] = trashed,with
```

## Method Details

scopeFilters(Builder $builder, $filter_terms): Builder
The scopeFilters() method is responsible for filtering the database records based on the provided filter terms. The method accepts two parameters:

- `$builder` - an instance of `Illuminate\Database\Eloquent\Builder` representing the current query builder instance.

- `$filter_terms` - an array of strings representing the filter terms to be used. Each filter term should be in the format column_name, filter_value.

The method returns an instance of `Illuminate\Database\Eloquent\Builder` representing the modified query builder instance.

The `scopeFilters()` method loops through each filter term defined and adds a filter clause to the query builder instance based on the format of the filter value. The filter value can be one of the following:

- A simple string value: The method adds a where clause to the query builder instance with the column name, filter operator, and filter value.

  - Query Example :- `public_url/index?filters[0]=is_active,0`

- A string value starting with `>` :- The method adds a where clause to the query builder instance with the column name and `>` operator followed by the filter value.

  - Query Example :- `public_url/index?filters[0]=price,>1000`

- A string value starting with `>=` :- The method adds a where clause to the query builder instance with the column name and `>=` operator followed by the filter value.

  - Query Example :- `public_url/index?filters[0]=price,>=1000`

- A string value starting with `<` :- The method adds a where clause to the query builder instance with the column name and `<` operator followed by the filter value.

  - Query Example :- `public_url/index?filters[0]=price,<1000`

- A string value starting with `<=` :- The method adds a where clause to the query builder instance with the column name and `<=` operator followed by the filter value.

  - Query Example :- `public_url/index?filters[0]=price,<=1000`

- A string value containing `_` :- The method assumes that the filter value is a range and adds a whereBetween clause to the query builder instance with the column name and the range values separated by an underscore (\_).

  - Query Example :- `public_url/index?filters[0]=price,1000_2000`

- A string value containing `!` :- The method assumes that the filter value is a range and adds a whereBetween clause to the query builder instance with the column name and the range values separated by an Exclamation mark (!).

  - Query Example :- `public_url/index?filters[0]=price,4000!7000`

- A string value of `trashed,only` :- The method adds a `onlyTrashed` clause to the query builder instance to filter only the soft-deleted records.

  - Query Example :- `public_url/index?filters[0]=trashed,only`

- A string value of `trashed,with` :- The method adds a `withTrashed` clause to the query builder instance to include the soft-deleted records.

  - Query Example :- `public_url/index?filters[0]=trashed,with`

## Use Multiple Filters

```json
public_url/index?filters[0]=price,>1000&filters[1]=status,paid,&filters[2]=is_active,0
```

## Conclusion

The Filterable trait is a useful addition to any Laravel 9 model that needs to filter the database records based on specific filter terms. It provides a simple and flexible way to filter the records based on any number of columns and filter operators, and can be easily customized to fit your specific needs.

---

# Sortable Trait

The Sortable trait provides a scope method that can be used to sort Eloquent query results based on one or multiple columns.

## Usage

To use the `sortable` trait, you can add it to your Eloquent model like so:

```php
use YoursJarvis\FiltersQuery\Sortable;

class MyModel extends Model
{
    use Sortable;

    // ...
}
```

Once the trait has been added, you can use the `sortBy` scope method to sort the query results:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Test;
use Illuminate\Http\Request;

class MyController extends Controller
{
  public function index(Request $request)
  {
    $data = MyModel::sortBy($request->sort_by);

    return response()->json(['data' => $data->get()], 200);
  }
}
```

This will sort the data by column in provided order.

If you call sortBy without any arguments, the results will be sorted in descending order by the model's created_at column by default.

## Query convention

You just need to pass filterable field and value in your query using following convention

```json
public_url/index?sort_by=price,desc
```

---

```text
Use `sortBy` trait at the starting of your method and then perform your custom logic otherwise it's might over right your custom logic.
```
