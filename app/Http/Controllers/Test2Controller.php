<?php

namespace  App\Http\Controllers; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use App\Lib\Dada\Dada;
use App\Lib\Printer\Printer;
use App\Listeners\Random;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Illuminate\Support\Carbon;

class Test2Controller extends Controller
{  // @todo AuthController 这里是要生成的类名字


    public function test(Request $request)
    {
         dump(Carbon::parse('+1 days')->timestamp);
         dump(Carbon::parse('-1 days')->timestamp);
         return;
         
         
         

        // $pay_id = 'PAY2019112517355696799';

        // $pay = DB::table('pay')
        //     ->where('pay_id', $pay_id)
        //     ->first();

        // $order = DB::table('order')
        //     ->where('pay_id', $pay_id)
        //     ->first();

        // $store = DB::table('store')
        //     ->where('store_id', $order->store_id)
        //     ->first();

        // $orderAddress = DB::table('order_address')
        //     ->where('id',  $order->address_id)
        //     ->first();

        // $snapshotInfo = DB::table('snapshot')
        //     ->where('order_id', $order->order_id)
        //     ->get();

        // $data = $snapshotInfo->map(function ($item) {
        //     $item->data = json_decode($item->data, true);
        //     $newItem = [
        //         'title' => $item->data['title'],
        //         'price' => $item->data['price'],
        //         'num' =>  $item->data['quantity'],
        //     ];
        //     return $newItem;
        // });

        // $printer = new Printer();

        // $header = [
        //     "<CB>" . $store->name . "</CB><BR>",
        //     '名称           单价  数量 金额<BR>',
        //     '--------------------------------<BR>',
        // ];

        // $footer = [
        //     '--------------------------------<BR>',
        //     '订单号：' . $order->order_id,
        //     '支付号：' . $pay->pay_id,
        //     '合计：' . number_format($pay->price, 2) . '元<BR>',
        //     '送货地点：' . $orderAddress->address . '<BR>',
        //     '联系电话：' .    $orderAddress->phone,
        //     '联系人：' .    $orderAddress->contacts,
        //     '订餐时间：' . $order->add_time,
        //     '备注：' . $order->remarks ? $order->remarks : '无' . '<BR><BR>',
        //     '<QR>https://www.yihuo-cloud.com/</QR>',
        // ];


        // $res = $printer->printData($header, $data, $footer, '921510805');

        return ["data" => $res];



        // $arr = [];
        // $arr[] = ['title' => '怪兽炒饭a套餐', 'price' => '1', 'num' => '1'];
        // $arr[] = ['title' => '怪兽炒饭a套餐', 'price' => '1', 'num' => '1'];
        // $arr[] = ['title' => '怪兽炒饭a套餐', 'price' => '1', 'num' => '1'];
        // $printer = new Printer();
        // $res = $printer->printData($arr, '921510805');
        // if ($res) {
        //     return response()->json([
        //         'code' => 1,
        //         'msg' => 'success',
        //         'data' => $res,
        //     ]);
        // } else {
        //     return response()->json([
        //         'code' => -1,
        //         'msg' => 'error',
        //         'data' => null,
        //     ]);
        // }
    }
}
