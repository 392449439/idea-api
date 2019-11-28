<?php

namespace  App\Http\Controllers\Mini\Vip; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Order\Order;
use App\Lib\Printer\Printer;
use App\Lib\Vip\Vip;
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


    public function buyTime(Request $request)
    {

        $day = $request->input('day');
        $price = ($day / 30) * 88;

        $order = new Order();
        $result = $order->create([
            "user_id" => $request->jwt->id,
            "price" => $price,
            "price" => 0.01,
            "snapshotData" => collect(['price' => $price, "day" => $day])->toJson(),
        ]);


        $pay_id = $result['pay_id'];
        $order_id = $result['order_id'];

        $pay_data =  $order->getPayData($request->jwt->openid, $pay_id, 'vip/pay_time_notify');

        return response()->json([
            'code' =>  $result ? 1 : -1,
            'msg' =>  $result  ? 'success' : 'error',
            'data' => [
                "pay_id" => $pay_id,
                "order_id" => $order_id,
                "pay_data" => $pay_data,
            ],
        ]);
    }

    public function payTimeNotify()
    {

        $app = Factory::payment([
            'app_id'             => 'wxb246a816f6d5bc96',
            'mch_id'             => '1563112131',
            'key'                => 'ZU30SEgmNbrmQdFNDR7gZZCF6uHLGDwC',
        ]);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            $pay_id = $message['out_trade_no'];

            $pay = DB::table('pay')->where('pay_id', $pay_id)->first();
            $order = DB::table('order')->where('pay_id', $pay_id)->first();

            if ($pay->state != 2) {
                DB::table('pay')
                    ->where('pay_id', $pay_id)
                    ->update([
                        'state' => 2,
                        'info' => json_encode($message)
                    ]);

                DB::table('order')
                    ->where('pay_id', $pay_id)
                    ->update(['state' => 2]);


                $snapshotInfo = DB::table('snapshot')->where('order_id', $order->order_id)->first();
                $vipData = json_decode($snapshotInfo->data, true);

                $day = $vipData['day'];
                $user_id = $order->user_id;

                $vip = new Vip();
                $vip->buyTime($user_id, $day);

                // ===================================================================================
                // 
                $printer = new Printer();

                $header = [
                    "<CB>会员充值</CB><BR>",
                    '--------------------------------<BR>',
                ];
                $data = [
                    "支付成功",
                ];
                $footer = [
                    '--------------------------------<BR>',
                    '订单号：' . $order->order_id,
                    '支付号：' . $pay->pay_id,
                ];

                $printer->printData($header, $data, $footer, '921510805');
            }

            return true;
        });
        return $response;
    }




    public function verifyVip(Request $request)
    {

        $user_id = $request->jwt->id;

        $vip = DB::table('vip_user_time')
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
