<?php

namespace  App\Http\Controllers; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use App\Listeners\Random;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Illuminate\Support\Carbon;

class TestController extends Controller
{  // @todo AuthController 这里是要生成的类名字

    public function test(Request $request)
    {
        $order_id = 'ORDER' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
        $pay_id = 'PAY' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
        dump($order_id);
        dump($pay_id);

        $random = new Random();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = $random->getRandom(16, 'yh');
        }
        dump($data);
    }
}
