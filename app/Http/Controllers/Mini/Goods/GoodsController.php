<?php

namespace  App\Http\Controllers\Mini\Goods; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;


class GoodsController extends Controller
{  // @todo AuthController 这里是要生成的类名字

	public function __construct($value = '')
	{ // 这里就是要生成的类文件的通用代码了
		# code...
	}

	public function list(Request $request)
	{

		$DB = DB::table('goods')
				->where('data_state',1)
				->where('is_up',1)
				->orderBy('add_time', 'desc');


		if ($request->filled('class_id')) {
			$DB->where('class_id', $request->input('class_id'));
		}

		$result = $DB->get();

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
		]);
	}
}
