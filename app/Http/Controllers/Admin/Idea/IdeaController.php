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
		
		$total = $DB->count();


		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 5));
		$DB->limit($request->input('page_size', 10));
		
		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => '查询成功',
			'data' => $result,
			'total' => $total * 1,
		];

	}

	// 想法详情
	public function info(Request $request)
	{

		$result = DB::table('thinking')
			->where('id', $request->input('id'))
			->first();

		return [
			'code' => $result ? 1 : -1,
			'msg' => '查询详情成功',
			'data' => $result,
		];
	}

	// 保存或者新增
	public function save(Request $request)
	{

		$data = $request->toArray();
		$data['user_id'] = 1;

			$result = DB::table('thinking')->insert($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => '添加成功',
				'data' => $result,
			]);
	}

	// 删除想法
	public function del(Request $request)
	{

		$result = DB::table('thinking')
			->where('id', $request->input('id'))
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => '删除成功',
			'data' => $result,
		];
	}

}
