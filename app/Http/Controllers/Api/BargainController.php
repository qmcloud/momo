<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityBargain;
use  App\Http\Resources\Bargain as BargainResource;
use  App\Http\Resources\BargainJoin as BargainJoinResource;
use  App\Http\Resources\BargainHelpList as BargainHelpListResource;
use App\Logic\ShopGoodsLogic;
use Illuminate\Support\Facades\DB;
use App\Logic\BargainLogic;

class BargainController extends ApiController
{

    // 获取砍价商品列表
    public function bargainList()
    {
        $outData = BargainResource::collection(ActivityBargain::getValidList());
        return $this->success($outData);
    }


    // 获取砍价商品详情
    public function bargainGoodsDetail(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $bargain = ActivityBargain::find($request->id);
        $outData = ShopGoodsLogic::getFullGoodsInfo($bargain->goods_id);
        $outData['bargain'] = new BargainResource($bargain);
        return $this->success($outData);
    }

    // 获取砍价详情
    public function bargainDetail(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'bargainId' => 'required',
                'goodsId' => 'required',
                'productId' => 'required',
            ],
            [
                'bargainId.required' => 'bargain_id参数缺失',
                'goodsId.required' => 'goods_id参数缺失',
                'productId.required' => 'product_id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $uid = 0;
        if (!empty(\Auth::user()->id)) {
            $uid = \Auth::user()->id;
        }
        $bargainLogic = new BargainLogic();
        $bargainLogic->setFromModels($request->input());
        $bargainLogic->set('uid', $uid);
        $bargainInfo = $bargainLogic->getBargainJoinDetail();
        if (empty($bargainInfo)) {
            $bargainInfo = $bargainLogic->bargainJoin();
        }
        $outData = new BargainJoinResource($bargainInfo);
        return $this->success($outData);
    }

    // 助力详情
    public function bargainHelpDetail(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'joinId' => 'required',
            ],
            [
                'joinId.required' => 'join_id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $uid = 3;
        if (!empty(\Auth::user()->id)) {
            $uid = \Auth::user()->id;
        }
        $bargainLogic = new BargainLogic();
        $bargainLogic->setFromModels($request->input());
        $bargainLogic->set('uid', $uid);
        $info = $bargainLogic->bargainHelpDetail();
        $outData['status'] = 0;
        $outData['uid'] = $uid;
        if (!empty($info)) {
            $outData['status'] = 1;
            $outData['price'] = $info->price;
        }
        return $this->success($outData);
    }


    // 发起助力
    public function bargainHandleHelp(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'joinId' => 'required',
            ],
            [
                'joinId.required' => 'join_id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $uid = 3;
        if (!empty(\Auth::user()->id)) {
            $uid = \Auth::user()->id;
        }
        $bargainLogic = new BargainLogic();
        $bargainLogic->setFromModels($request->input());
        $bargainLogic->set('uid', $uid);
        $info = $bargainLogic->bargainHelpDetail();
        if (empty($info)) {
            // 执行助力
            DB::beginTransaction();
            try {
                $info = $bargainLogic->bargainHelp();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->failed('助力失败' . $e->getMessage(), 545);
            }
        }
        $outData = new BargainHelpListResource($info);
        return $this->success($outData);
    }


}