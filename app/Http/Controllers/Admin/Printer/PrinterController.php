<?php

namespace App\Http\Controllers\Admin\Printer;
// @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Printer\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrinterController extends Controller
{  // @todo UserController 这里是要生成的类名字


    //打印机添加和修改
    public function save(Request $request)
    {

        $Feieyun = new Printer();


        $sn = $request->input('item_sn');
        $key = $request->input('item_key');

        $status =  $Feieyun->status($sn);

        if ($status['ret'] == '1002') {
            // 未绑定，加入到飞鹅
            $result =  $Feieyun->add("$sn#$key");
        } else {
            // 已绑定，不加入到飞鹅
        }

        $result = DB::table('printer')->insert($request->all());

        return response()->json([
            'code' => $result >= 0 ? 1 : -1,
            'msg' => $result >= 0 ? 'success' : 'error',
            'data' => $result,
        ]);
    }



    //打印机详情
    public function info(Request $request)
    {
        if ($request->filled('id')) {
            $id = $request->input('id');
        } else {
            $id = $request->jwt->id;
        }

        $result = DB::table('printer')
            ->where('id', $id)
            ->first();

        return response()->json([
            'code' => $result ? 1 : -1,
            'msg' => $result ? 'success' : 'error',
            'data' => $result,
            'id' => $id,
        ]);
    }


    public function list(Request $request)
    {
        $DB = DB::table('printer')->orderBy('add_time', 'desc');

        if ($request->filled('id')) {
            $DB->where('id', $request->input('id'));
        }

        //        if ($request->filled('company_ids')) {
        //            $DB->where('company_id', $request->input('company_ids'));
        //        }
        //
        //        if ($request->filled('phone')) {
        //            $DB->where('phone', 'like', '%' . $request->input('phone') . '%');
        //        }
        //        if ($request->filled('name')) {
        //            $DB->where('name', 'like', '%' . $request->input('name') . '%');
        //        }
        //
        //        if ($request->filled('state')) {
        //            $DB->where('state', $request->input('state'));
        //        }
        //
        //        if ($request->filled('user_type')) {
        //            $DB->where('user_type', $request->input('user_type'));
        //        }


        $total = $DB->count() + 0;

        $DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
            ->limit($request->input('page_size', 10));

        $result = $DB->get();

        return response()->json([
            'code' => $result ? 1 : -1,
            'msg' => $result ? 'success' : 'error',
            'data' => $result,
            'total' => $total,
        ]);
    }


    public function del(Request $request)
    {
        $result = DB::table('printer')
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return response()->json([
            'code' => $result >= 0 ? 1 : -1,
            'msg' => $result >= 0 ? 'success' : 'error',
            'data' => $result,
        ]);
    }
}
