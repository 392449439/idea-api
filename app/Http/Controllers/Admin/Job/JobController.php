<?php

namespace  App\Http\Controllers\Admin\Job; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{

	// 职位列表
	public function list(Request $request)
	{

		$DB = DB::table('job') //定义表
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

	// 职位详情
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

	// 保存或者新增
	public function save(Request $request)
	{

		if ($request->filled('id')) {
			// 保存
			$result = DB::table('job') //定义表
				->where('id', $request->input('id'))  //前端传过来的id
				->update($request->all());  //获取全部
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加
			$result = DB::table('job')->insert($request->all());  //插入新的数据
			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	// 删除接口
	public function del(Request $request)
	{

		$result = DB::table('job') //定义表
			->where('id', $request->input('id')) //前台传过来的id
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

}
