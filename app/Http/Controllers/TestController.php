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
        $random = new Random();
        echo ('<h1>16位</h1>');
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $appid = $random->getRandom(16);
            echo ("<div class='item'>($i). 16位appid: <b>$appid</b></div>");
        }

        $random = new Random();
        echo ('<h1>32位</h1>');
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $appid = $random->getRandom(32);
            echo ("<div class='item'>($i). 16位appid: <b>$appid</b></div>");
        }
    }
}
?>

<style>
    .item {
        font-size: 14px;
        line-height: 1.5;
    }
</style>