<?php
/**
 * sqc @小T科技 2018.06.07
 *
 *
 */
namespace App\Logic;

use App\Models\ShopGoods;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use App\Logic\ShopCouponLogic;
use Carbon\Carbon;
use function EasyWeChat\Kernel\Support\generate_sign;

class Buy
{

    /**
     * 会员信息id
     * @var string
     */
    private $_user_id = '';

    /**
     * 下单数据
     * @var array
     */
    private $_order_data = array();

    /**
     * 下单地区数据
     * @var array
     */
    private $_address_data = array();

    /**
     * 表单数据
     * @var array
     */
    private $_post_data = array();

    public function __construct()
    {
    }


    /**
     * 执行购买
     * @param int $user_id // 购买者
     * @param array $buydata // 购买的商品
     * @param array $address //收货地址
     * @param object $request // 表单数据
     * @return array
     */
    public function buyStep($request, $buy_info, $address, $user_id)
    {
        $this->_user_id = $user_id;
        $this->_order_data = $buy_info;
        $this->_post_data = $request;
        $this->_address_data = $address;
        try {
            DB::beginTransaction();
            //第1步 执行下单
            $order_info = $this->_createOrderStep1();
            return $order_info;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

    }


    /**
     * 生成订单
     * @param array $input
     * @throws Exception
     * @return array array(支付单sn,订单列表)
     */
    private function _createOrderStep1()
    {
        $paycode = $this->makePaySn($this->_user_id);
        //订单数据
        $order_insert = array();
        $order_insert['order_sn'] = $this->makeOrderSn($paycode); // 订单编号
        $order_insert['uid'] = $this->_user_id;
        $order_insert['pay_name'] = '微信支付';// 目前只有微信支付
        $order_insert['pay_id'] = 1;// 目前只有微信支付
        $order_insert['order_status'] = ShopOrder::STATUS_WAIT_PAY;// 待支付
        $order_insert['shipping_status'] = ShopOrder::SHIPING_STATUS_WAIT_SEND;// 待发货
        $order_insert['pay_status'] = ShopOrder::PAY_WAIT;// 待支付下单
        $order_insert['actual_price'] = $this->_order_data['actualPrice'];// 订单实际要支付的金额
        $order_insert['order_price'] = $this->_order_data['orderTotalPrice'];// 订单总价
        $order_insert['goods_price'] = $this->_order_data['goodsTotalPrice'];// 商品总价
        $order_insert['freight_price'] = $this->_order_data['freightPrice'];// 配送费用
        $order_insert['coupon_id'] = $this->_order_data['checkedCouponId'];// 使用的优惠券id
        $order_insert['coupon_price'] = $this->_order_data['couponPrice'];// 优惠金额
        $order_insert['add_time'] = Carbon::now();// 订单生成时间

        // 收货地址
        $order_insert['country'] = $this->_address_data['country_id'];// 国家id 1 中国
        $order_insert['province'] = $this->_address_data['province_id'];// 省市id
        $order_insert['city'] = $this->_address_data['city_id'];// 城市id
        $order_insert['district'] = $this->_address_data['district_id'];// 区县、街道id
        $order_insert['address'] = $this->_address_data['address'];// 详细地址
        $order_insert['consignee'] = $this->_address_data['user_name'];// 收件人
        $order_insert['mobile'] = $this->_address_data['mobile'];// 收件人手机号

        //用户留言
        $order_insert['postscript'] = empty($this->_order_data['postscript']) ? '用户无留言' : $this->_order_data['postscript'];

        $order_goods_insert = array(); // 订单附表的数据

        // 执行插入
        $order_model = ShopOrder::create($order_insert);
        if (!$order_model->id) {
            throw new \Exception('订单保存失败[未生成支付单]');
        }
        foreach ($this->_order_data['checkedGoodsList'] as $k => $va) {
            if ($va['number']) {
                $order_goods_insert[] = [
                    'order_id' => $order_model->id,
                    'goods_id' => $va['goods_id'],
                    'product_id'=> $va['product_id'],
                    'goods_name' => $va['goods_name'],
                    'retail_price' => $va['retail_price'],
                    'market_price' => $va['market_price'],
                    'list_pic_url' => $va['list_pic_url'] ? $va['list_pic_url'] : '',
                    'number' => $va['number'],
                ];
                $re1 = $this->addSaleNum($va['goods_id'], $va['number']);
                if (!$re1) {
                    throw new \Exception('订单保存失败[商品库存不足]');
                }
            }
        }
        $order_goods_model = DB::table('shop_order_goods')->insert($order_goods_insert);
        // 清除购物车商品
        $cartRe = CartLogic::clearCart($this->_user_id);
        if($this->_order_data['checkedCouponId']){
            // 使用优惠券
            ShopCouponLogic::useCoupon($this->_user_id,$this->_order_data['checkedCouponId']);
        }

        if ($order_goods_model) {
            DB::commit();
            return $order_model->toArray();
        } else {
            $this->pay_log('order_goods_insert.error' . var_export($order_goods_insert, true));
            throw new \Exception('订单商品保存失败');
        }
    }

    public function addSaleNum($id, $num = 1)
    {
        $goods = ShopGoods::lockForUpdate()->find($id);
        if (empty($goods) || $goods->goods_number < $num) {
            return false;
        }
        $goods->sell_volume = $goods->sell_volume + $num;
        $goods->goods_number = $goods->goods_number - $num;
        return $goods->save();
    }

    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makePaySn($member_id)
    {
        return mt_rand(10, 99)
        . sprintf('%010d', time() - 946656000)
        . sprintf('%03d', (float)microtime() * 1000)
        . sprintf('%03d', (int)$member_id % 1000);
    }

    /**
     * 订单编号生成规则
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param $pay_sn
     * @return string
     */
    public function makeOrderSn($pay_sn)
    {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        return (date('y', time()) % 9 + 1) . sprintf('%013d', $pay_sn) . sprintf('%02d', $num);
    }


    // 支付第一步
    public function pay_step1($attributes, $openid)
    {
        $time = time();
        $app = Factory::payment(config('wechat.payment.default'));
        $result = $app->order->unify([
            'body' => '小T商城',
            'detail' => '小T商城的订单',
            'out_trade_no' => $attributes['order_sn'],
            'total_fee' => $attributes['actual_price'] * 100,
            'trade_type' => 'JSAPI',
            'openid' => $openid,
        ]);
        $this->pay_log('order:' . var_export($attributes, true) . '  result:' . var_export($result, true));
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            // 如果成功生成统一下单的订单，那么进行二次签名 二次签名的参数必须与下面相同
            $params = [
                'appId' => config('wechat.payment.default.app_id'),
                'timeStamp' => (string)time(),
                'nonceStr' => $result['nonce_str'],
                'package' => 'prepay_id=' . $result['prepay_id'],
                'signType' => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));
            return $params;
        } else {
            // 返回错误信息
            $this->pay_log('json_prepare' . var_export($result, true));
            return false;
        }
    }

    /**
     * 记录日志
     */
    private function pay_log($msg)
    {
        $msg = date('H:i:s') . "|" . $msg . "\r\n";
        $msg .= '| GET:' . var_export($_GET, true) . "\r\n";
        file_put_contents('./log/pay' . date('Y-m-d') . ".log", $msg, FILE_APPEND);
    }


}
