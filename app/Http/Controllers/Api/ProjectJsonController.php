<?php

namespace App\Http\Controllers\Api;

use App\Logics\ProjectControl;
use Illuminate\Http\Request;

class ProjectJsonController extends ApiController
{
	public function getProjectTypes(Request $request)
	{
		return ProjectControl::getJsonForProjectTypes();
	}

	public function getProjectFuncTypesByTypeId(Request $request)
	{
		$pid = 0;
		$where = [];
		$data = [];
		if (isset($request->q))
		{
			$pid = $request->q; 
		}
		if ($pid < 0)	return $data;
		$where = [$pid];
		$data = ProjectControl::getProjectFuncTypesByOneLevel($where);
		return $data;
	}

	public function getProjectModelsByFunctypeId(Request $request)
	{
		$pid = 0;
		$where = [];
		$data = [];
		if (isset($request->q))
		{
			$pid = $request->q;
		}
		if ($pid < 0)	return $data;
		$where = [$pid];
		$data = ProjectControl::getProjectModelsByOneLevel($where);
		return $data;
	}
}