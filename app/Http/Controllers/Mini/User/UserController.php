<?php

namespace  App\Http\Controllers\Mini\User; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{  // @todo UserController 这里是要生成的类名字


	public function save(Request $request)
	{

		if ($request->jwt->id) {

			$result = DB::table('user')
				->where('id', $request->jwt->id)
				->update($request->all());

			return [
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			];
		} else { }
	}

	public function info(Request $request)
	{

		$id = $request->jwt->id;
		if ($request->jwt->id) {
			$id = $request->jwt->id;
		} else {
			$id = $request->input('id');
		}

		$result =	DB::table('user')
			->where('id', $id)
			->first();

		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' =>  $result ? 'success' : 'error',
			'data' => $result,
		]);
	}


	public function list(Request $request)
	{


		$DB = DB::table('user')->orderBy('add_time', 'desc');


		if ($request->filled('id')) {
			$DB->where('id', $request->input('id'));
		}

		if ($request->filled('company_ids')) {
			$DB->where('company_id', $request->input('company_ids'));
		}

		if ($request->filled('phone')) {
			$DB->where('phone', 'like',  '%' . $request->input('phone') . '%');
		}
		if ($request->filled('name')) {
			$DB->where('name', 'like',  '%' . $request->input('name') . '%');
		}

		if ($request->filled('state')) {
			$DB->where('state', $request->input('state'));
		}
		$total = $DB->count() + 0;

		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));

		$result = $DB->get();


		$result->transform(function ($user) {
			if ($user->company_id) {
				$user->company_name =	DB::table('company')->where('id', $user->company_id)->value('name');
			} else {
				$user->company_name = '';
			}

			$user->power_group_name =	DB::table('power_group')->where('id', $user->power_group_id)->value('name');

			return $user;
		});

		return response()->json([
			'code' => $result  ? 1 : -1,
			'msg' =>  $result  ? 'success' : 'error',
			'data' => $result,
			'total' => $total,
		]);
	}


	public function del(Request $request)
	{


		$result = DB::table('user')
			->whereIn('id', $request->input('ids'))
			->delete();

		return response()->json([
			'code' => $result >= 0 ? 1 : -1,
			'msg' =>  $result >= 0 ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function verifyVip(Request $request)
	{

		$user_id = $request->jwt->id;

		$vip = DB::table('vip')
			->where('user_id', $user_id)
			->first();


		if (time() < $vip->end_time) {
			// 未到期
			return response()->json([
				'code' => 1,
				'msg' =>  '未到期',
				'data' => $vip,
			]);
		} else {
			return response()->json([
				'code' => -1,
				'msg' =>  '已到期',
				'data' => $vip,
			]);
		}
	}
}
