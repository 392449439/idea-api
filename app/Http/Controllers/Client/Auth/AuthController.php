<?php

namespace  App\Http\Controllers\Client\Auth; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
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

	public function login(Request $request)
	{

		$code = $request->input('code');

		$appid = 'wx754474ce7640bd0c';
		$secret = '810c6117c5d0d61392744fde8e8cd010';
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";

		$data = $this->http($url);

		$access_token = $data['access_token'];
		$openid = $data['openid'];
		$unionid = $data['unionid'];
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
		$wx_userInfo = $this->http($url);

		$User = DB::table('user');

		$is = $User->where('unionid', $unionid)->exists();

		if (!$is) {
			// 不存在，就添加
			$userData = [
				"openid" => $openid,
				"unionid" => $unionid,
				"wx_head" => $wx_userInfo['headimgurl'],
				"wx_name" => $wx_userInfo['nickname'],
			];
			$User = DB::table('user');
			$User->insert($userData);
		}

		$userInfo = $User->where('unionid', $unionid)->first();

		$jwt = encrypt(json_encode($userInfo));

		return [
			"code" => 1,
			"msg" => "success",
			"data" => $userInfo,
			'jwt' => $jwt,
		];
	}


	public function http($url, $data = [])
	{
		// $data = ['code' => 1, "token" => 2];
		$data = collect($data);
		$data =	$data->map(function ($v, $k) {
			return "$k=$v";
		});
		$data = $data->values()->toArray();
		$data = implode(';', $data);

		$curlobj = curl_init();
		curl_setopt($curlobj, CURLOPT_URL, $url);
		curl_setopt($curlobj, CURLOPT_USERAGENT, "user-agent:Mozilla/5.0 (Windows NT 5.1; rv:24.0) Gecko/20100101 Firefox/24.0");
		curl_setopt($curlobj, CURLOPT_HEADER, 0);          //启用时会将头文件的信息作为数据流输出。这里不启用
		curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, 1);  //如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE
		curl_setopt($curlobj, CURLOPT_POST, 1);            //如果你想PHP去做一个正规的HTTP POST，设置这个选项为一个非零值。这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用。
		curl_setopt($curlobj, CURLOPT_POSTFIELDS, $data);  //需要POST的数据
		curl_setopt($curlobj, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded;  
															charset=utf-8", "Content-length: " . strlen($data)));
		$rtn = curl_exec($curlobj);
		return json_decode($rtn, true);
	}
}
