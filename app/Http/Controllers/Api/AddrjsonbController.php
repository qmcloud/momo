<?php

namespace App\Http\Controllers\Api;

use App\Models\Addrjson;
use App\Models\Dorm;
use Illuminate\Http\Request;

class AddrjsonbController extends ApiController
{
	public function city(Request $request)
	{
		$pid = 0;
		$where = [];
		$city = [];
		if (isset($request->q))
		{
			$pid = $request->q; 
		}
		if ($pid <= 0)	return $city;
		
		$where['area_parent_id'] = $pid;
		
		$addr = Addrjson::select('area_id','area_name');
		if (!is_null($where))	$addr = $addr->where($where);
		$city = $addr->get()->toArray();
		
		return $city;
	}
	public function dorm(Request $request)
	{
		$dorms = Dorm::select('id','name')->where('sid',$request->q)->get();
		return $dorms->toArray();
	}
}