<?php

namespace  App\Http\Controllers\Client\Idea; // @todo: 这里是要生成类的命名空间

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
			->where('data_state', 1)
			->orderBy('add_time', 'desc');

		$total = $DB->count();


		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 5));
		$DB->limit($request->input('page_size', 5));

		$result = $DB->get();

		$result->map(function ($el) use ($request) {
			$el->userInfo = DB::table('user')->where('id', $el->user_id)->first();
			// 取得赞的数量
			$el->up = DB::table('up')->where('idea_id', $el->id)->count() * 1;
			// 当前用户是否赞过了
			$el->isUp = DB::table('up')
				->where('user_id', $request->iwt->id)
				->where('idea_id', $el->id)
				->exists();

			return $el;
		});

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
		$data['user_id'] = $request->jwt->id;

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
			->update(['data_state' => 0]);


		$result = DB::table('up')
			->where('idea_id', $request->input('id'))
			->update(['data_state' => 0]);

		return [
			'code' => $result ? 1 : -1,
			'msg' => '删除成功',
			'data' => $result,
		];
	}

	// 点赞接口
	public function up(Request $request)
	{

		$is = DB::table('up')
			->where('idea_id', $request->input('id'))
			->where('user_id', $request->jwt->id)
			->exists();
		$isUp = false;

		if ($is) {
			// 已经点赞
			// 删除点赞
			$result = DB::table('up')
				->where('idea_id', $request->input('id'))
				->where('user_id', $request->jwt->id)
				->delete();
			$isUp = false;
		} else {
			// 还没点赞
			// 添加点赞
			$result = DB::table('up')
				->insert([
					"user_id" => $request->jwt->id,
					"idea_id" => $request->input('id')
				]);
			$isUp = true;
		}

		$up = DB::table('up')->where('idea_id', $request->input('id'))->count() * 1;

		return [
			'code' => 1,
			'msg' => '',
			'isUp' => $isUp,
			'up' => $up,
		];
	}
}
