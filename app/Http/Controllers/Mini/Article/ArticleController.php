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

		$DB->where('is_up', $request->input('is_up', 1));


		$total = $DB->count();


		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 5));
		$DB->limit($request->input('page_size', 10));



		// if ($request->filled('app_id')) {
		// 	$DB->where('app_id', $request->input('app_id'));
		// } else {
		// 	$DB->where('app_id', $request->appInfo->app_id);
		// }

		$result = $DB->get();

		$result->map(function ($item) {
			$item->img_list = json_decode($item->img_list);
			return $item;
		});

		return [
			'code' => $result->count(),
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
			'total' => $total * 1,
		];
	}

	// 文章列表
	public function vipList(Request $request)
	{


		$isLogin = !!$request->jwt;
		$isVip = false;
		$noVipMaxTotal = 2;

		if ($isLogin) {
			$user_id = $request->jwt->id;
			$vip = DB::table('vip')
				->where('user_id', $user_id)
				->first();
			if (time() < $vip->end_time) {
				// 未到期
				$isVip = true;
			} else {
				$isVip = false;
			}
		}


		$DB = DB::table('paper')
			->where('type', $request->type)
			->orderBy('add_time', 'desc');


		if ($request->filled('title')) {
			$DB->where('title', 'like',  '%' . $request->input('title') . '%');
		}

		$DB->where('is_up', $request->input('is_up', 1));


		$total = $noVipMaxTotal;

		if ($isLogin && $isVip) {
			// 登录了，且是会员的情况下，正常显示
			$total = $DB->count();

			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 5));
			$DB->limit($request->input('page_size', 10));
		} else {
			// 未登录或不是vip让他仅显示10个

			$DB->offset(0);
			$DB->limit($noVipMaxTotal);
		}

		$result = $DB->get();

		$result->map(function ($item) {
			$item->img_list = json_decode($item->img_list);
			return $item;
		});

		return [
			'code' => $result->count(),
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
			'total' => $total * 1,
			'login' => $isLogin,
			'vip' => $isVip,
			'noVipMaxTotal' => $noVipMaxTotal,
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
		} else {
			$data['img_list'] = '[]';
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

	// 文章详情
	public function getPhone(Request $request)
	{


		$user_id = $request->jwt->id;

		$vip = DB::table('vip')
			->where('user_id', $user_id)
			->first();


		$contact = DB::table('paper')
			->where('data_state', 1)
			->where('id', $request->input('id'))
			->value('contact');


		if (time() < $vip->end_time) {
			// 未到期
			return response()->json([
				'code' => 1,
				'msg' =>  '未到期',
				'data' => $contact,
			]);
		} else {
			return response()->json([
				'code' => -1,
				'msg' =>  '已到期',
				'data' => null,
			]);
		}

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}
}
