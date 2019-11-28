<?php

namespace  App\Lib\Order;

use EasyWeChat\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Order
{

    public $appid = 'wxb246a816f6d5bc96';
    public $mch_id = '1563112131';
    public $mch_secret = 'ZU30SEgmNbrmQdFNDR7gZZCF6uHLGDwC';

    public function create($config)
    {

        /**
         * 组成模型
         */
        $SnapshotDB = DB::table('snapshot');
        $OrderDB = DB::table('order');
        $PayDB = DB::table('pay');

        $order_id = 'ORDER' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
        $pay_id = 'PAY' . Carbon::now()->format('YmdHis') . rand(10000, 99999);





        /** 用户 */
        $user_id = $config['user_id'];

        /** 价格 */
        $price = $config['price'];

        /** 快照数据 */
        $snapshotData = $config['snapshotData'];

        /** 组成快照 */
        $snapshotInfo = [
            'order_id' => $order_id,
            'user_id' => $user_id,
            'type' => 'VIP_BUY',
            'title' => '会员充值',
            'data' => json_encode($snapshotData)
        ];



        /** 组成订单 */

        $orderInfo = [
            'order_id' => $order_id,
            'pay_id' => $pay_id,
            'user_id' => $user_id,
            'price' => $price,
        ];

        /** 组成支付单数据 */
        $payInfo = [
            'pay_id' => $pay_id,
            'price' => $price,
            'type' => 'VIP_BUY',
        ];

        $SnapshotDB->insert($snapshotInfo);
        $PayDB->insert($payInfo);
        $OrderDB->insert($orderInfo);

        return [
            "pay_id" => $pay_id,
            "order_id" => $order_id,
        ];
    }

    public function getPayData($openid, $pay_id, $notify_url)
    {

        $config = [
            // 必要配置
            'app_id'             => $this->appid,
            'mch_id'             => $this->mch_id,
            'key'                => $this->mch_secret,   // API 密钥
            'notify_url'         => url($notify_url),     // 你也可以在下单时单独设置来想覆盖它
        ];

        $app = Factory::payment($config);
        $jssdk = $app->jssdk;

        $payInfo = DB::table('pay')->where('pay_id', $pay_id)->first();
        $result = $app->order->unify([
            'body' => '平台充值',
            'out_trade_no' => $payInfo->pay_id,
            'total_fee' => $payInfo->price * 100,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' =>  $openid,
        ]);
        if ($result['return_code'] != 'SUCCESS') {
            return false;
        }
        $config = $jssdk->sdkConfig($result['prepay_id']); // 返回数组
        return $config;
    }
}
