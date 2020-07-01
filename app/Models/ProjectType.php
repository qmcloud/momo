<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ProjectType as ProjectTypeResource;

class ProjectType extends Model
{
    const STATUS_ON = 1;
    protected $table = 'project_type';

    static  public function getProjectInfo($where){
        return new ProjectTypeResource(static::where($where)->first());
    }

    public function getCarouselImgsAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setCarouselImgsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['carousel_imgs'] = json_encode($pictures);
        }
    }
}