<?php

namespace  App\Http\Controllers\Mini\Store; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{

	// 门店列表
	public function list(Request $request)
	{

		$DB = DB::table('store')
			->orderBy('add_time', 'desc');

		if ($request->filled('app_id')) {
			$DB->where('app_id', $request->input('app_id'));
		} else {
			$DB->where('app_id', $request->appInfo->app_id);
		}

		$result = $DB->get();
		$result->map(function ($item) {
			$item->label = explode(',', $item->label);
			return $item;
		});

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	// 门店详情
	public function info(Request $request)
	{

		$result = DB::table('store')
			->where('data_state', 1)
			->where('store_id', $request->input('store_id'))
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

		if ($request->filled('store_id')) {

			$result = DB::table('store')
				->where('store_id', $request->input('store_id'))
				->update($request->all());

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {

			$result = DB::table('store')->insert($request->all());

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

		$result = DB::table('store')
			->where('store_id', $request->input('store_id'))
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}
}
