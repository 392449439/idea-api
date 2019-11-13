<?php

namespace  App\Http\Controllers\Admin\Goods; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Listeners\Random;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{

	// 门店列表
	public function list(Request $request)
	{

		$DB = DB::table('goods') //定义表
			->orderBy('add_time', 'desc'); //排序

		$total = $DB->count() + 0;

		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));
		
		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
			'total' => $total,
		];
	}

	// 门店详情
	public function info(Request $request)
	{

		$result = DB::table('goods')
			->where('id', $request->input('id'))
			->first();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];

	}

	// 保存或者新增
	public function save(Request $request)
	{

		if ($request->filled('id')) {

			$result = DB::table('goods')
				->where('id', $request->input('id'))
				->update($request->all());

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {

			$result = DB::table('goods')->insert($request->all());

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}

	}

	// 删除门店接口
	public function del(Request $request)
	{

		$result = DB::table('goods')
			->where('id', $request->input('id'))
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

}
