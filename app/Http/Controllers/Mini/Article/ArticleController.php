<?php

namespace  App\Http\Controllers\Mini\Article; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{

	// 文章列表
	public function list(Request $request)
	{

		$DB = DB::table('paper')
			->where('type', $request->type)
			->orderBy('add_time', 'desc');


		if ($request->filled('title')) {

			$DB->where('title', 'like',  '%' . $request->input('title') . '%');
		}

		if ($request->filled('is_up')) {

			$DB->where('is_up', $request->input('is_up'));
		}

		// if ($request->filled('app_id')) {
		// 	$DB->where('app_id', $request->input('app_id'));
		// } else {
		// 	$DB->where('app_id', $request->appInfo->app_id);
		// }

		$result = $DB->get();
		// $result->map(function ($item) {
		// 	$item->label = explode(',', $item->label);
		// 	return $item;
		// });

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	// 文章详情
	public function info(Request $request)
	{

		$result = DB::table('paper')
			->where('data_state', 1)
			->where('id', $request->input('id'))
			->first();


		$result->img_list = json_decode($result->img_list);


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
		if (Arr::has($data, 'img_list')) {
			$data['img_list'] = json_encode($data['img_list']);
		}

		if ($request->filled('id')) {

			$result = DB::table('paper')
				->where('id', $request->input('id'))
				->update($data);

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {


			$result = DB::table('paper')->insert($data);

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

		$result = DB::table('paper')
			->where('id', $request->input('id'))
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}
}
