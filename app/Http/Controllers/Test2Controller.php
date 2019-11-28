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
        // //  dump(Carbon::parse('+1 days')->timestamp);
        // //  dump(Carbon::parse('-1 days')->timestamp);
        // $data = [];
        // $data['user_id'] = 20;
        // $data['end_time'] = Carbon::parse('+30 days')->timestamp;
        // $data['max'] = 30;

        // $result = DB::table('vip_user_count')->where('user_id', 20)->update($data);
        // return $result;
    }
}
