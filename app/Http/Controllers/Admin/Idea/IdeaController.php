<?php

namespace  App\Http\Controllers\Admin\Idea; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Listeners\Random;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class IdeaController extends Controller
{

	// 想法列表
	public function list(Request $request)
	{

		$DB = DB::table('thinking') //定义表
				->orderBy('add_time', 'desc');
		
		
		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];

	}

	// 想法详情
	// public function info(Request $request)
	// {

	// 	$result = DB::table('goods')
	// 		->where('id', $request->input('id'))
	// 		->first();

	// 	return [
	// 		'code' => $result ? 1 : -1,
	// 		'msg' => $result ? 'success' : 'error',
	// 		'data' => $result,
	// 	];
	// }

	// 保存或者新增
	public function save(Request $request)
	{

		$data = $request->toArray();
		$data['user_id'] = 1;

		// if ($request->filled('id')) {

		// 	$result = DB::table('goods')
		// 		->where('id', $request->input('id'))
		// 		->update($data);

		// 	return response()->json([
		// 		'code' => $result >= 0 ? 1 : -1,
		// 		'msg' =>  $result >= 0 ? 'success' : 'error',
		// 		'data' => $result,
		// 	]);
		// } else {

			$result = DB::table('thinking')->insert($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => '添加成功',
				'data' => $result,
			]);

		// }
	}

	// 删除想法
	// public function del(Request $request)
	// {

	// 	$result = DB::table('goods')
	// 		->where('id', $request->input('id'))
	// 		->delete();

	// 	return [
	// 		'code' => $result ? 1 : -1,
	// 		'msg' => $result ? 'success' : 'error',
	// 		'data' => $result,
	// 	];
	// }

	// public function volume(Request $request)
	// {
	// 	$store_id = $request->input('store_id');

	// 	$result = DB::select("

	// 		SELECT 
	// 			snapshot.goods_id,
	// 			goods.title,
	// 			goods.goods_head,
	// 			COUNT( snapshot.goods_id ) AS sales_volume
	// 		FROM
	// 			snapshot,
	// 			goods
	// 		WHERE 
	// 			snapshot.goods_id  = goods.id
	// 			AND
	// 			snapshot.store_id  = '$store_id'
	// 		GROUP BY
	// 			snapshot.goods_id
	// 		ORDER BY
	// 			sales_volume DESC
	// 		LIMIT 1,10

	// 	");

	// 	return [
	// 		'code' => $result ? 1 : -1,
	// 		'msg' => $result ? 'success' : 'error',
	// 		'data' => $result,
	// 	];
	// }

}
