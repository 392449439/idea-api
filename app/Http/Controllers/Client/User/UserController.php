<?php

namespace  App\Http\Controllers\Client\User; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{  // @todo UserController 这里是要生成的类名字


	public function save(Request $request)
	{
		if ($request->filled('id')) {
			$data = [];
			$data['phone'] = $request->input('phone');
			if ($request->filled('pwd1')) {
				$data['pwd'] = md5($_ENV['APP_KEY'] .  $request->input('pwd1'));
			}
			$data['name'] = $request->input('name');
			// $data['power_group_id'] = $request->input('power_group_id');

			$result = DB::table('user')
				->where('id', $request->input('id'))
				->update($data);

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加

			/**检查是否重复 */

			if (DB::table('user')->where('phone', $request->input('phone'))->first()) {
				return response()->json([
					'code' => -1,
					'msg' =>  '用户已存在！',
					'data' => null,
				]);
			} else {
				$data = [];
				$data['phone'] = $request->input('phone');
				$data['pwd'] = md5($_ENV['APP_KEY'] .  $request->input('pwd1'));
				$data['name'] = $request->input('name');
				$data['user_type'] = $request->input('user_type');

				$result = DB::table('user')->insert($data);
				return response()->json([
					'code' => $result ? 1 : -1,
					'msg' => $result ? 'success' : 'error',
					'data' => $result,
				]);
			}
		}
	}

	public function info(Request $request)
	{

		$id = '';
		if ($request->filled('id')) {
			$id = $request->input('id');
		} else {
			$id = $request->jwt->id;
		}

		$result =	DB::table('user')
			->where('id', $id)
			->first();


		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' =>  $result ? 'success' : 'error',
			'data' => $result,
			'id' => $id,
		]);
	}


	public function list(Request $request)
	{


		$DB = DB::table('user')
			->where('domain_id',$request->domain_id)
			->orderBy('add_time', 'desc');


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

		if ($request->filled('user_type')) {
			$DB->where('user_type', $request->input('user_type'));
		}


		$total = $DB->count() + 0;

		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));

		$result = $DB->get();


		// $result->transform(function ($user) {
		// 	return $user;
		// });

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

	public function setpwd(Request $request) {
		
		// if ($request->filled('id')) {
			$data = [];
			if ($request->filled('pwd1')) {
				$data['pwd'] = md5($_ENV['APP_KEY'] .  $request->input('pwd1'));
			}
			$result = DB::table('user')
				->where('id', $request->jwt->id)
				->update($data);
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		// } 
	}


	// 
}
