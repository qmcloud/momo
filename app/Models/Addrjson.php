<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addrjson extends Model
{
    protected $table = 'br_area';	// 地址的表名
    protected $primaryKey = 'area_id';	// 地址的表名
    
    public function province(Request $request)
    {
    	$q = $request->get('q');
    	return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);

    }
}
