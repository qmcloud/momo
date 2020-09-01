<?php

namespace App\Models;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Article extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'demo_articles';

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public static function grid($callback)
    {
        return new Grid(new static, $callback);
    }

    public static function form($callback)
    {
        return new Form(new static, $callback);
    }
}
