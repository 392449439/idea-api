<?php

namespace  App\Http\Controllers; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use App\Listeners\Random;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;


class TestController extends Controller
{  // @todo AuthController 这里是要生成的类名字

    public function test(Request $request)
    {

        $random = new Random();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = $random->getRandom(16, 'yh');
        }
        dump($data);
    }
}
