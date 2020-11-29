<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'demo_videos';

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'demo_taggables');
    }
}