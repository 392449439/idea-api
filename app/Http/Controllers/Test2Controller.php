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

        $pay_id = 'PAY2019112517355696799';

        $order_id = DB::table('order')
            ->where('pay_id', $pay_id)
            ->value('order_id');

        $snapshotInfo = DB::table('snapshot')
            ->where('order_id', $order_id)
            ->get();

        $data = $snapshotInfo->map(function ($item) {
            $item->data = json_decode($item->data, true);
            $newItem = [
                'title' => $item->data['title'],
                'price' => $item->data['price'],
                'num' =>  $item->data['quantity'],
            ];
            return $newItem;
        });

        $printer = new Printer();
        $res = $printer->printData($data, '921510805');
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
