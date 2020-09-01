<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'demo_images';

    public function author()
    {
        return $this->belongsTo(User::class, 'uploader');
    }
}
