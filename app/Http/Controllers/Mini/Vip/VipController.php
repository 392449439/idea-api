<?php

namespace  App\Http\Controllers\Mini\Vip; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VipController extends Controller
{  // @todo UserController 这里是要生成的类名字


    public function priceList(Request $request)
    {

        $DB = DB::table('vip_price')->orderBy('price', 'asc');
        $result = $DB->get();
        return response()->json([
            'code' => $result->count(),
            'msg' =>  $result  ? 'success' : 'error',
            'data' => $result,
        ]);
    }


    public function buy(Request $request)
    {

        $DB = DB::table('vip_price')->where('id', $request->input('vip_price_id'));
        $result = $DB->first();
        $price = $result->price;
        $day = $result->day;

        /**
         * 组成模型
         */
        $SnapshotDB = DB::table('snapshot');
        $OrderDB = DB::table('order');
        $PayDB = DB::table('pay');


        $order_id = 'ORDER' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
        $pay_id = 'PAY' . Carbon::now()->format('YmdHis') . rand(10000, 99999);


        $app_type = $request->appInfo->app_type; //app的类型，在这里指订单类型，订单来源
        $app_id = $request->appInfo->app_id;

        /**
         * 组成快照
         */

        // $goodsData = [];
        $snapshotInfo = [
            'order_id' => $order_id,
            'user_id' => '',
            'app_id' => $app_id,
            'type' => 'VIP_BUY',
            'title' =>  '会员充值',
            'data' =>    collect(['price' => $price, "day" => $day])->toJson(),
        ];


        /**
         * 组成订单
         */


        $orderInfo = [];

        $orderInfo['order_id'] = $order_id;
        $orderInfo['pay_id'] = $pay_id;
        $orderInfo['user_id'] = $request->jwt->id;
        $orderInfo['app_id'] = $app_id;
        $orderInfo['price'] = $price;
        $orderInfo['app_type'] = $app_type;
        $orderInfo['type'] = 'VIP_BUY';


        /**
         * 组成支付单数据
         */

        $payInfo = [];
        $payInfo['pay_id'] = $pay_id;
        $payInfo['price'] = $price;
        $payInfo['price'] = 0.01;
        $payInfo['app_type'] = $app_type;
        $payInfo['app_id'] = $app_id;
        $payInfo['type'] = 'VIP_BUY';


        $SnapshotDB->insert($snapshotInfo);
        $PayDB->insert($payInfo);
        $OrderDB->insert($orderInfo);

        $pay_data =  $this->getPayData($request, $pay_id);
        return [
            'code' => 1,
            'msg' => 'success',
            'data' => [
                "pay_id" => $pay_id,
                "order_id" => $order_id,
                "pay_data" => $pay_data
            ],
        ];


        return  [$result];
    }

    private function getPayData($request, $pay_id)
    {
        $app_id = $request->appInfo->app_id;
        $config = [
            // 必要配置
            'app_id'             => $request->appInfo->wx_appid,
            'mch_id'             => $request->appInfo->wx_mch_id,
            'key'                => $request->appInfo->wx_mch_secret,   // API 密钥
            'notify_url'         => url("pay/vip_notify_url/{$app_id}"),     // 你也可以在下单时单独设置来想覆盖它
        ];

        $app = Factory::payment($config);
        $jssdk = $app->jssdk;

        $payInfo = DB::table('pay')->where('pay_id', $pay_id)->first();
        $result = $app->order->unify([
            'body' => '平台充值',
            'out_trade_no' => $payInfo->pay_id,
            'total_fee' => $payInfo->price * 100,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' =>  $request->jwt->openid,
        ]);
        if ($result['return_code'] != 'SUCCESS') {
            return [
                'code' => -1,
                'data' => $result,
                'msg' => 'error',
            ];
        }
        $config = $jssdk->sdkConfig($result['prepay_id']); // 返回数组
        return $config;
    }



    public function verifyVip(Request $request)
    {

        $user_id = $request->jwt->id;

        $vip = DB::table('vip')
            ->where('user_id', $user_id)
            ->first();


        if (time() < $vip->end_time) {
            // 未到期
            return response()->json([
                'code' => 1,
                'msg' =>  '未到期',
                'data' => $vip,
            ]);
        } else {
            return response()->json([
                'code' => -1,
                'msg' =>  '已到期',
                'data' => $vip,
            ]);
        }
    }
}
