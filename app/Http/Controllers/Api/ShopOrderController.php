<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Logic\CartLogic;
use App\Models\ShopCart;
use App\Logic\AddressLogic;
use App\Logic\ShopCouponLogic;
use App\Logic\BargainLogic;
use App\Logic\Buy;
use App\Http\Resources\ShopMyCoupon;

class ShopOrderController extends ApiController
{
    // 校验购物车商品
    public function checkout(Request $request)
    {
        if (empty(\Auth::user()->id)) {
            $this->user_id = 0;
        } else {
            $this->user_id = \Auth::user()->id;
        }
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required',
            ],
            [
                'addressId.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $outData = CartLogic::getCheckedGoodsList($this->user_id);
        $couponInfo = ShopCouponLogic::checkedCoupon($this->user_id, $request->couponId, $outData['goodsTotalPrice']);
        $outData['checkedAddress'] = AddressLogic::getOneAddr($request->addressId, $this->user_id); // 选择地址
        $outData['checkedCoupon'] = $couponInfo['checkedCoupon']; // 选择的优惠券
        $outData['couponList'] = ShopMyCoupon::collection(ShopCouponLogic::getAvailableCouponListByGoodsPrice($outData['goodsTotalPrice'], $this->user_id)); //  优惠券列表
        $outData['couponPrice'] = $couponInfo['couponPrice']; // 选中的优惠金额
        $outData['actualPrice'] = PriceCalculate($outData['orderTotalPrice'], '-', $outData['couponPrice']); // 真实付款金额
        return $this->success($outData);
    }

    // 立即购买
    public function payNow(Request $request)
    {
        if (empty(\Auth::user()->id)) {
            $this->user_id = 0;
        } else {
            $this->user_id = \Auth::user()->id;
        }
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required',
                'productId' => 'required',
                'goodsId' => 'required',
                'buynumber' => 'required',
                'bargainId' => 'integer',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        if ($request->bargainId) {
            $bargainLogic = new BargainLogic();
            $bargainLogic->setFromModels($request->all());
            $bargainLogic->set('uid', $this->user_id);
            $outData = $bargainLogic->getGoodsByBargain();
            if(is_string($outData)){
                return $this->failed([$outData],536);
            }
        } else {
            $outData = CartLogic::getBuyGoodsById($request->goodsId, $request->buynumber, 1, $request->productId);
        }
        $couponInfo = ShopCouponLogic::checkedCoupon($this->user_id, $request->couponId, $outData['goodsTotalPrice']);
        $outData['checkedAddress'] = AddressLogic::getOneAddr($request->addressId, $this->user_id); // 选择地址
        $outData['checkedCoupon'] = $couponInfo['checkedCoupon']; // 选择的优惠券
        $outData['couponList'] = ShopMyCoupon::collection(ShopCouponLogic::getAvailableCouponListByGoodsPrice($outData['goodsTotalPrice'], $this->user_id)); //  优惠券列表
        $outData['couponPrice'] = $couponInfo['couponPrice']; // 选中的优惠金额
        $outData['actualPrice'] = PriceCalculate($outData['orderTotalPrice'], '-', $outData['couponPrice']); // 真实付款金额
        return $this->success($outData);
    }


    // 提交订单用来生成订单
    public function orderSubmit(Request $request)
    {
        if (empty(\Auth::user()->id)) {
            $this->user_id = 0;
        } else {
            $this->user_id = \Auth::user()->id;
        }
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required',
            ],
            [
                'addressId.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        if ($request->goodsId) {
            $orderData = CartLogic::getBuyGoodsById($request->goodsId, $request->buynumber, 1, $request->productId);
        } else if ($request->bargainId) {
            $bargainLogic = new BargainLogic();
            $bargainLogic->setFromModels($request->all());
            $bargainLogic->set('uid', $this->user_id);
            $orderData = $bargainLogic->getGoodsByBargain();
        } else {
            $orderData = CartLogic::getCheckedGoodsList($this->user_id);
        }
        $checkedAddress = AddressLogic::getOneAddr($request->addressId, $this->user_id); // 选择地址
        if (empty($checkedAddress)) {
            return $this->failed('未查到用户收货地址，请检查您的收货地址', 401);
        }
        $couponInfo = ShopCouponLogic::checkedCoupon($this->user_id, $request->couponId, $orderData['goodsTotalPrice']);
        $orderData['checkedCouponId'] = $request->couponId??0; // 选择的优惠券
        $orderData['checkedCoupon'] = $couponInfo['checkedCoupon']; // 选择的优惠券
        $orderData['couponList'] = ShopCouponLogic::getAvailableCouponListByGoodsPrice($orderData['goodsTotalPrice'], $this->user_id); //  优惠券列表
        $orderData['couponPrice'] = $couponInfo['couponPrice']; // 选中的优惠金额
        $orderData['actualPrice'] = PriceCalculate($orderData['orderTotalPrice'], '-', $orderData['couponPrice']); // 真实付款金额
        $orderData['postscript'] = $request->postscript??'暂无留言';
        $buyModel = new Buy();
        $buyRe = $buyModel->buyStep($request, $orderData, $checkedAddress, $this->user_id);
        if (empty($buyRe['error'])) {
            return $this->success($buyRe);
        }
        return $this->failed($buyRe['error'], 403);
    }

}