<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;


class Option extends Model
{
    use AdminBuilder;

    //use Faker;

    protected $table = 'options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function base()
    {
        return $this->hasOne(Option::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function friends()
    {
        return $this->belongsToMany(User::class, 'demo_friends', 'user_id', 'friend_id')->withPivot('remark');
    }


    # 5.5 版本

    public function searchableAs()
    {
        return 'options';
    }
}

