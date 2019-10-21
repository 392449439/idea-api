<?php

namespace  App\Http\Controllers\Official\Job; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{

	public function list(Request $request)
	{

		$result = DB::table('job') //定义表
			->orderBy('add_time', 'desc') //排序
			->select(['id', 'name', 'type']) //想要查询的字段
			->where('is_up', '1')
			->get(); //取列表


		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	public function info(Request $request)
	{

		$result = DB::table('job') //定义表
			->where('id', $request->input('id')) //前台传过来的id
			->first(); //获取数据


		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	public function save(Request $request)
	{

		if ($request->filled('id')) {
			// 保存
			// $request->toArray()

			$result = DB::table('job')
				->where('id', $request->input('id'))
				->update($request->all());
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加
			$result = DB::table('job')->insert($request->all());
			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	public function delete(Request $request)
	{

		
		
	}

}
