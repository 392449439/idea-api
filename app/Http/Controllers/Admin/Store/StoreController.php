<?php

namespace  App\Http\Controllers\Admin\Store; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Listeners\Random;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{

	// 门店列表
	public function list(Request $request)
	{

		$DB = DB::table('store') //定义表
			->where('domain_id',$request->domain_id)
			->orderBy('add_time', 'desc'); //排序

		$total = $DB->count() + 0;


		if ($request->filled('page')) {
			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10));
		}

		if ($request->filled('page_size')) {
			$DB->limit($request->input('page_size', 10));
		}


		if ($request->filled('app_id')) {
			$DB->where('app_id', $request->input('app_id'));
		}


		$result = $DB->get();
		$result->map(function ($item) {
			$item->label = explode(',', $item->label);
			$item->appInfo = DB::table('app')->where('app_id', $item->app_id)->first();
			return $item;
		});

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

		$result = DB::table('store')
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

		$data = $request->toArray();

		if ($request->filled('store_id')) {

			if (Arr::has($data, 'label')) {
				if (gettype($data['label']) == 'array') {
					$data['label'] = implode(',', $data['label']);
				}
			}



			$result = DB::table('store')
				->where('store_id', $request->input('store_id'))
				->update($data);

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {

			$random = new Random();

			$data['store_id'] = $random->getRandom(16, 'S_');

			$result = DB::table('store')->insert($data);

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
