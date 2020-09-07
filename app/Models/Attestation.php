<?php

namespace App\Models;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attestation extends Model
{

    protected $table = 'attestations';

    public $sortable = [

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
