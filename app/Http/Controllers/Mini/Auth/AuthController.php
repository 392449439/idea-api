<?php

namespace  App\Http\Controllers\Mini\Auth; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Illuminate\Support\Arr;

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
			'app_id' => 'wx9f4a9bdc95bcc3d7',
			'secret' => '7d073c85829782b9d690b40e81f12bb5',
			'response_type' => 'array',
		]);
		$res = $app->auth->session($request->input('code'));
		$decryptedData = $app->encryptor->decryptData($res['session_key'], $request->input('iv'), $request->input('encryptedData'));


		// avatarUrl: "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLt51Sq1c4aicK3OVMpOazFlDzfTe5yJUP1PDpKyCDyJeBiauzAlIsBMKUqfSRuud2XKWJUPJTickVtw/132"
		// city: "Xuhui"
		// country: "China"
		// gender: 1
		// language: "zh_CN"
		// nickName: "敲代码的"
		// openId: "oj92Q4vVfs8FJVrnUxIDT1JW-Tas"
		// province: "Shanghai"
		// unionId: "oMJGssz88vjRihjnyBVI9CMCifkg"
		// watermark:
		// 			appid: "wx9f4a9bdc95bcc3d7"
		// 			timestamp: 1573177103



		return $decryptedData;
		return [
			"code" => 1,
			"msg" => "success",
			"data" =>	"oraT74g14WCH6eKSv4_brTr4fmt4"
		];
	}

	public function login(Request $request)
	{

		// 'wx9f4a9bdc95bcc3d7'
		// 7d073c85829782b9d690b40e81f12bb5
		$app = Factory::miniProgram([
			'app_id' => $request->appInfo->wx_appid,
			'secret' =>  $request->appInfo->wx_secret,
			'response_type' => 'array',
		]);

		$res = $app->auth->session($request->input('code'));
		$decryptedData = $app->encryptor->decryptData($res['session_key'], $request->input('iv'), $request->input('encryptedData'));
		$openid = $decryptedData['openId'];
		$unionId = Arr::has($decryptedData, 'unionId') ? $decryptedData['unionId'] : '';


		// avatarUrl: "https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLt51Sq1c4aicK3OVMpOazFlDzfTe5yJUP1PDpKyCDyJeBiauzAlIsBMKUqfSRuud2XKWJUPJTickVtw/132"
		// city: "Xuhui"
		// country: "China"
		// gender: 1
		// language: "zh_CN"
		// nickName: "敲代码的"
		// openId: "oj92Q4vVfs8FJVrnUxIDT1JW-Tas"
		// province: "Shanghai"
		// unionId: "oMJGssz88vjRihjnyBVI9CMCifkg"
		// watermark:
		// 			appid: "wx9f4a9bdc95bcc3d7"
		// 			timestamp: 1573177103

		$DB = DB::table('user');

		$result = $DB
			->where('app_id', $request->appInfo->app_id)
			->where('openid', $openid)
			->first();

		if (!$result) {
			// 没有，创建
			$DB->insert([
				"openid" => $openid,
				"unionId" => $unionId,
				"app_id" =>  $request->appInfo->app_id,
				"wx_info" => json_encode($decryptedData),
				"name" => $decryptedData['nickName'],
				"head_img" =>  $decryptedData['avatarUrl'],

			]);

			$result = $DB
				->where('app_id', $request->appInfo->app_id)
				->where('openid', $openid)
				->first();
		}


		$jwt = encrypt(json_encode($result));

		return [
			"code" => 1,
			"msg" => "success",
			"data" => $result,
			'jwt' => $jwt,
		];
	}
}
