<?php

namespace  App\Http\Controllers\Mini\Address; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{

	// 图文列表
	public function list(Request $request)
	{

		$DB = DB::table('address')
			->orderBy('add_time', 'desc');
		
		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	// 图文详情
	public function info(Request $request)
	{

		$result = DB::table('address')
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
			
			$result = DB::table('address')
				->where('id', $request->input('id'))
				->update($request->all());

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);

		} else {
			
			$result = DB::table('address')->insert($request->all());

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);

		}

	}

	// 删除接口
	// public function del(Request $request)
	// {

	// 	$result = DB::table('perpre')
	// 		->where('id', $request->input('id'))
	// 		->delete();

	// 	return [
	// 		'code' => $result ? 1 : -1,
	// 		'msg' => $result ? 'success' : 'error',
	// 		'data' => $result,
	// 	];
	// }

}
