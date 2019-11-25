<?php

namespace  App\Http\Controllers\Admin\Auth; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{  // @todo AuthController 这里是要生成的类名字

	public function __construct($value = '')
	{ // 这里就是要生成的类文件的通用代码了
		# code...
	}

	public function create(Request $request)
	{
		// $name = $request->input('phone');
		// 		$results = DB::select("SELECT * FROM user");
		dump('success');
		die;
		$data = [
			'phone' => 'root',
			'pwd' => md5($_ENV['APP_KEY'] . '123'),
			'power_group_id' => 1,
		];

		DB::table('user')->insert($data);


		return response()->json([
			'code' => 1,
			'msg' => '',
			'data' => $data
		]);
	}
	public function login(Request $request)
	{
		$user =	DB::table('user')
			->where('phone', $request->input('phone'))
			->first('pwd');

		if ($user && $user->pwd == md5($_ENV['APP_KEY'] . $request->input('user_pwd'))) {

			$user =	DB::table('user')
				->where('phone', $request->input('phone'))
				->first([
					'id',
					// 'company_id',
					'user_type',
					'phone',
					'name',
					// 'power_group_id',
					// 'state',
					'add_time',
					'edit_time',
					'data_state',
				]);

			$jwt = encrypt(json_encode($user));

			return response()->json([
				'code' => 1,
				'msg' => '登录成功',
				'data' => $user,
				'jwt' => $jwt,
			]);
		} else {
			return response()->json([
				'code' => -1,
				'msg' => '登录失败',
				'data' => null
			]);
		}
	}
}
