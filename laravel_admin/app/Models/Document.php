<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Document extends Model implements Sortable
{
    use SoftDeletes, SortableTrait;

    protected $table = 'demo_documents';

    public $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];

    public static $privileges = [
        1 => '私有',
        2 => '好友可见',
        3 => '分享可见',
        4 => '公开',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
