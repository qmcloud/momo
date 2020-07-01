<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSpecItem extends Model
{

    protected $table = "shop_spec_item";
    protected $fillable = ['id', 'spec_id', 'item'];

    public function specification()
    {
        return $this->hasOne(ShopSpecification::class, 'spec_id');
    }

}
