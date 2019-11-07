<?php

namespace  App\Http\Controllers\Mini\Auth; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;


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
	public function openid(Request $request)
	{

		$app = Factory::miniProgram([
			'app_id' => 'wxd2b47e26632f45cc',
			'secret' => '0e45b998ce388ebbbf7a50d81a7865b6',
			'response_type' => 'array',
		]);
		$res = $app->auth->session($request->input('code'));
		$decryptedData = $app->encryptor->decryptData($res['session_key'], $request->input('iv'), $request->input('encryptedData'));
		return $decryptedData;

		// openid: "oraT74g14WCH6eKSv4_brTr4fmt4"
		// session_key: "yCBnE2wBcfa53JmdhoXLxg=="

		return [
			"code" => 1,
			"msg" => "success",
			"data" =>	"oraT74g14WCH6eKSv4_brTr4fmt4"
		];
	}

	public function login(Request $request)
	{
		if (!$request->filled('openid')) {
			return [
				"code" => -1,
				"msg" => "openid 不能为空",
				"data" => null
			];
		}


		$openid = $request->input('openid');
		$DB = DB::table('user');

		$result = $DB->where('openid', $request->input('openid'))->first();

		if ($result) {
			// 已有

		} else {
			// 没有，创建
			$DB->insert(["openid" => $openid]);
			$result = $DB->where('openid', $request->input('openid'))->first();
		}


		$jwt = encrypt(json_encode($result));



		return [
			"code" => 1,
			"msg" => "success",
			"data" => $result,
			'jwt' => $jwt,
		];
	}

	public function list(Request $request)
	{

		$DB = DB::table('goods_class') //定义表
			->orderBy('add_time', 'desc'); //排序

		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	public function goodsList(Request $request)
	{

		$DB = DB::table('goods_class') //定义表
			->orderBy('add_time', 'desc'); //排序

		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}
}
