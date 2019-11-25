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

        $pay = DB::table('pay')
            ->where('pay_id', $pay_id)
            ->get();
        return ["data" => $pay];



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
