<?php

namespace  App\Http\Controllers\Msg; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MsgController extends Controller
{  // @todo ItemController 这里是要生成的类名字
   
    public function save(Request $request)
    {
        if ($request->filled('id')) {
            // 保存
            // $request->toArray()

            $result = DB::table('item')
                ->where('id', $request->input('id'))
                ->update(Arr::except($request->toArray(), ['company_name']));
            return response()->json([
                'code' => $result >= 0 ? 1 : -1,
                'msg' =>  $result >= 0 ? 'success' : 'error',
                'data' => $result,
            ]);
        } else {
            // 添加
            $result = DB::table('item')->insert($request->all());
            return response()->json([
                'code' => $result ? 1 : -1,
                'msg' => $result ? 'success' : 'error',
                'data' => $result,
            ]);
        }
    }


    public function info(Request $request)
    {
        $result =    DB::table('item')
            ->where('id', $request->input('id'))
            ->first();
        $result->company_name = DB::table('company')->where('id', $result->company_id)->value('name');

        return response()->json([
            'code' => $result ? 1 : -1,
            'msg' => $result ? 'success' : 'error',
            'data' => $result,
        ]);
    }

    public function list(Request $request)
    {


        $DB = DB::table('msg')->orderBy('add_time', 'desc');

        $company_ids = [];


        if ($request->jwt->company_id) {
            $company_ids = [$request->jwt->company_id];
        }

        if (count($company_ids) > 0) {
            $DB->whereIn('company_id', $company_ids);
        }


        $total = $DB->count() + 0;


        if ($request->filled('page')) {
            $DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
                ->limit($request->input('page_size', 10));
        }

        $result = $DB->get();


        return response()->json([
            'code' => 1,
            'msg' => 'success',
            'data' => $result,
            'total' => $total,
        ]);
    }
}
