<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultipleImage extends Model
{
    protected $table = 'demo_multiple_images';

    public function getPicturesAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setPicturesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['pictures'] = json_encode($pictures);
        }
    }
}
