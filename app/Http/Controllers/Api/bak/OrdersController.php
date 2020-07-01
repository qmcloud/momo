<?php
/**
 * User: liuhao
 * Date: 18-3-9
 * Time: 上午9:50
 */

namespace App\Http\Controllers\Api;


use App\Logics\OrderLogic;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrdersController extends ApiController
{
    //获取配送订单列表
    public function deliveryOrderList(Request $request)
    {
        $userID = Auth::user()->id;
        if (!$userID) {
            return $this->failed('非法的用户请求', 401);
        }

        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'status' => 'required',
            ],
            [
                'status.required' => '订单状态缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }

        $orderStatus = $request->input('status');

        $orderLogic = new OrderLogic();

//        $userID = 7;

        list($resaultFlag, $data) = $orderLogic->getDeliveryOrderList($userID, $orderStatus);

        if ($resaultFlag) {
            return $this->success($data);
        } else {
            list($code, $errMsg) = $this->transErrMsg($data);
            return $this->failed($errMsg, $code);
        }
    }

    //确认接收订单
    public function receiveOrder(Request $request)
    {
//        $userID = 7;
        $userID = Auth::user()->id;
        if (!$userID) {
            return $this->failed('非法的用户请求', 401);
        }

        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'orderID' => 'required',
            ],
            [
                'orderID.required' => 'orderID缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }

        $orderID = $request->input('orderID');
        $orderLogic = new OrderLogic();
        $orderLogic->isInSchool = true;
        $orderLogic->optionUserID = $userID;

        list($resaultFlag, $errMsg) = $orderLogic->receiveOrder($orderID);
        if ($resaultFlag) {
            return $this->success([]);
        } else {
            list($code, $msg) = $this->transErrMsg($errMsg);
            return $this->failed($msg, $code);
        }
    }


    //确认配送订单
    public function deliveryOrder(Request $request)
    {
        $userID = Auth::user()->id;
        if (!$userID) {
            return $this->failed('非法的用户请求', 401);
        }

        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'orderID' => 'required',
            ],
            [
                'orderID.required' => 'orderID缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }

        $orderID = $request->input('orderID');

        $orderLogic = new OrderLogic();
        $orderLogic->isInSchool = true;
        list($resaultFlag, $errMsg) = $orderLogic->deliveryOrder($orderID);
        if ($resaultFlag) {
            return $this->success([]);
        } else {
            list($code, $msg) = $this->transErrMsg($errMsg);
            return $this->failed($msg, $code);
        }
    }

    //订单详情
    public function getOrderDetail(Request $request)
    {
        $userID = Auth::user()->id;
        if (!$userID) {
            return $this->failed('非法的用户请求', 401);
        }

        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'orderID' => 'required',
            ],
            [
                'orderID.required' => 'orderID缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }

        $orderID = $request->input('orderID');
        $orderLogic = new OrderLogic();

        $orderLogic->optionUserID = $userID;

        list($resaultFlag, $data) = $orderLogic->getOrderDetail($orderID);

        if ($resaultFlag) {
            return $this->success($data);
        } else {
            list($code, $msg) = $this->transErrMsg($data);
            return $this->failed($msg, $code);
        }

    }

    //用户收货
    public function completeOrder(Request $request)
    {
        $userID = Auth::user()->id;
        if (!$userID) {
            return $this->failed('非法的用户请求', 401);
        }

        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'orderID' => 'required',
            ],
            [
                'orderID.required' => 'orderID缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }

        $orderID = $request->input('orderID');
        $orderLogic = new OrderLogic();

        $orderLogic->optionUserID = $userID;

        list($resaultFlag, $data) = $orderLogic->completeOrder($orderID);
        if ($resaultFlag) {
            return $this->success($data);
        } else {
            list($code, $msg) = $this->transErrMsg($data);
            return $this->failed($msg, $code);
        }

    }

    protected function transErrMsg($errMsg, $code = 401)
    {
        $msg = $errMsg;

        if (is_string($errMsg)) {
            $pos = strpos($errMsg, '|');

            if ($pos !== false) {
                $code = substr($errMsg, 0, $pos);
                $msg = substr($errMsg, $pos + 1);
            }
        }

        return [$code, $msg];
    }
}