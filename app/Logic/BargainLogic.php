<?php
/**
 * sqc @小T科技 2018.03.06
 *
 *
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ShopGoods;
use App\Models\ShopCollect;
use Illuminate\Support\Carbon;
use App\Helpers\Logic\HelperLogic;
use App\Models\ActivityBargain;
use App\Models\ActivityBargainJoin;
use App\Models\ActivityBargainHelp;
use App\Models\ShopProduct;

class BargainLogic
{
    use HelperLogic;
    private $bargain;

    public function getBargin()
    {
        $this->bargain = ActivityBargain::find($this->bargainId);

    }

    public function getBargainJoinDetail()
    {
        if($this->joinId){
            return ActivityBargainJoin::find($this->joinId);
        }
        return ActivityBargainJoin::where(['bargain_id' => $this->bargainId, 'uid' => $this->uid, 'goods_id' => $this->goodsId, 'product_id' => $this->productId])->first();
    }

    /**
     * 参与砍价
     */
    public function bargainJoin()
    {
        $this->getBargin();
        $bargain_price = 0.00;
        if (empty($this->bargain)) {
            throw new \Exception('未查到相关数据', 451);
        }
        $bargain_price = $this->bargain->goods->retail_price;
        if ($this->productId) {
            $productInfo = ShopProduct::where(['id' => $this->productId, 'goods_id' => $this->goodsId])->first();
            if (empty($productInfo)) {
                throw new \Exception('未查到相关数据', 452);
            }
            $bargain_price = $productInfo->retail_price;
        }
        $model = new ActivityBargainJoin();
        $model->uid = $this->uid;
        $model->bargain_id = $this->bargainId;
        $model->goods_id = $this->bargain->goods_id;
        $model->product_id = $this->productId;
        $model->bargain_price_min = $this->bargain->min_price;
        $model->bargain_price = $bargain_price;
        $model->price = 0.00;
        $model->help_num = 0;
        $model->status = ActivityBargainJoin::STATE_ON;
        $re = $model->save();
        if (!$re) {
            throw new \Exception('数据插入失败', 531);
        }
        return $model;
    }

    /**
     * 助力砍价
     */
    public function bargainHelp()
    {
        $joinInfo = ActivityBargainJoin::lockForUpdate()->find($this->joinId);
        if (empty($joinInfo)) {
            throw new \Exception('未查到相关数据', 452);
        }
        $this->bargainId = $joinInfo->bargain_id;
        $this->getBargin();
        if (empty($this->bargain)) {
            throw new \Exception('未查到相关数据', 451);
        }
        $this->checkBargain($this->bargain, $joinInfo);
        $validPrice = $joinInfo['bargain_price'] - $joinInfo['bargain_price_min'];
        $model = new ActivityBargainHelp();
        $model->uid = $this->uid;
        $model->bargain_id = $joinInfo->bargain_id;
        $model->join_id = $this->joinId;
        $model->bargain_uid = $joinInfo->uid;
        $model->price = $this->getBragainPrice($validPrice);
        $re = $model->save();
        if (!$re) {
            throw new \Exception('数据插入失败', 531);
        }
        $re1 = $joinInfo->decrement('bargain_price', $model->price);
        if (!$re1) {
            throw new \Exception('数据更新失败', 532);
        }
        $re2 = $joinInfo->increment('price', $model->price);
        if (!$re2) {
            throw new \Exception('数据更新失败', 533);
        }
        $re3 = $joinInfo->increment('help_num', 1);
        if (!$re3) {
            throw new \Exception('数据更新失败', 534);
        }
        return $model;
    }

    /**
     * 助力砍价详情
     */
    public function bargainHelpDetail()
    {
        return ActivityBargainHelp::where(['join_id' => $this->joinId, 'uid' => $this->uid])->first();
    }

    private function getBragainPrice($validPrice)
    {
        $tmp = mt_rand($this->bargain->bargain_min_price * 100, $this->bargain->bargain_max_price * 100);
        $tmp = (int)($tmp / 100);
        if ($tmp > $validPrice) {
            $tmp = $validPrice;
        }
        return $tmp ? $tmp : 1;
    }

    /**
     * 校验砍价信息
     */
    private function checkBargain($bargain, $joinInfo = [])
    {
        if ($bargain['status'] != ActivityBargain::STATE_ON) {
            throw new \Exception('商品未开启砍价', 416);
        }
        $nowDate = time();
        if (strtotime($bargain['start_time']) > $nowDate) {
            throw new \Exception('砍价未开始', 417);
        }

        if (strtotime($bargain['stop_time']) < $nowDate) {
            throw new \Exception('砍价已结束', 418);
        }
        if (empty($joinInfo)) {
            return true;
        }
        if ($joinInfo['help_num'] == $bargain['help_num']) {
            throw new \Exception('助力已完成', 419);
        }
        if ($joinInfo['bargain_price_min'] >= $joinInfo['bargain_price']) {
            throw new \Exception('助力已完成', 421);
        }
    }

    /**
     * 校验用户砍价信息是否可以下单
     */
    private function validJoin($joinInfo)
    {
        if (empty($joinInfo)) {
            throw new \Exception('数据不存在', 429);
        }
        if ($joinInfo['is_addorder'] == ActivityBargainJoin::ORDER_ADD) {
            throw new \Exception('已下单', 421);
        }
        if ($joinInfo['status'] != ActivityBargainJoin::STATE_ON) {
            throw new \Exception('不可用', 423);
        }

    }

    public function getGoodsByBargain($format = 1)
    {
        try {
            $this->getBargin();
            $joinInfo = $this->getBargainJoinDetail();
            $this->checkBargain($this->bargain);
            $this->validJoin($joinInfo);
            $products = $joinInfo->product;
            $goods = $joinInfo->goods;
            $product_goods_spec_item_names = '';
            if (!empty($products)) {
                $product_goods_spec_item_names = $products->goods_spec_item_names;
            }
            $checkedGoodsList[] = [
                "goods_id" => $joinInfo->goods_id,
                "product_id" => $joinInfo->product_id,
                "goods_name" => $joinInfo->goods->goods_name . ' ' . $product_goods_spec_item_names,
                "market_price" => $joinInfo->goods->retail_price,
                "retail_price" => $joinInfo->bargain_price,
                "number" => $this->buynumber,
                'freight_price' => $joinInfo->goods->freight_price,
                "primary_pic_url" => $format ? config('filesystems.disks.oss.url') . '/' . $joinInfo->goods->primary_pic_url : $joinInfo->goods->primary_pic_url,
                "list_pic_url" => $format ? config('filesystems.disks.oss.url') . '/' . $joinInfo->goods->primary_pic_url : $joinInfo->goods->primary_pic_url,
            ];
            $goodsTotalPrice = 0.00;
            foreach ($checkedGoodsList as $goodsVal) {
                $goodsTotalPrice = PriceCalculate($goodsTotalPrice, '+', PriceCalculate($goodsVal['retail_price'], '*', $this->buynumber));
            }
            $freightPrice = array_sum(array_pluck($checkedGoodsList, 'freight_price'));
            return [
                'checkedGoodsList' => $checkedGoodsList,// 商品列表
                'goodsTotalPrice' => $goodsTotalPrice,// 商品总价格
                'freightPrice' => $freightPrice,// 商品运费总和
                'orderTotalPrice' => PriceCalculate($goodsTotalPrice, '+', $freightPrice)
            ];
        } catch (\Exception $e) {
//            var_dump($e->getMessage());
            return $e->getMessage();
        }
    }

}
