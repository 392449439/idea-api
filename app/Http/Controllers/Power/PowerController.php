<?php

namespace  App\Http\Controllers\Power; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PowerController extends Controller
{  // @todo ItemController 这里是要生成的类名字

	public function save(Request $request)
	{
		if ($request->filled('id')) {

			$power = $request->input('power');

			$group_id = $request->input('id');
			DB::table('power_group')
				->where('id', $request->input('id'))
				->update(['name' => $request->input('name')]);

			$data = [];
			foreach ($power as $k => $v) {
				$item = [];
				$item['group_id'] = $group_id;
				$item['power_id'] = $v;
				$data[] = $item;
			}

			DB::table('power')
				->where('group_id', $request->input('id'))
				->delete();

			$result = DB::table('power')->insert($data);

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加
			// $result = DB::table('item')->insert($request->all());

			$power = $request->input('power');

			$group_id = DB::table('power_group')->insertGetId(['name' => $request->input('name')]);

			$data = [];
			foreach ($power as $k => $v) {
				$item = [];
				$item['group_id'] = $group_id;
				$item['power_id'] = $v;
				$data[] = $item;
			}


			$result = DB::table('power')->insert($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	public function info(Request $request)
	{
		$result =	DB::table('power_group')
			->where('id', $request->input('id'))
			->first();


		$result->power = DB::table('power')
			->where('group_id', $request->input('id'))
			->pluck('power_id');


		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}


	public function delGroup(Request $request)
	{

		$result =	DB::table('power_group')
			->where('id', $request->input('id'))
			->delete();

		DB::table('power')
			->where('group_id', $request->input('id'))
			->delete();


		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}




	public function listGroup(Request $request)
	{

		$DB = DB::table('power_group')->orderBy('add_time', 'desc');

		if ($request->filled('name')) {
			$DB->where('name', 'like',  '%' . $request->input('name') . '%');
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

	public function getUserPower(Request $request)
	{

		$power_group_id = $request->jwt->power_group_id;


		$result = DB::table('power')
			->where('group_id',	$power_group_id)
			->pluck('power_id');


		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
		]);
	}
}
