<?php

namespace  App\Http\Controllers\Mini\Order; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PayController extends Controller
{


	public function getMini(Request $request)
	{

		$config = [
			// 必要配置
			'app_id'             => $request->appInfo->wx_appid,
			'mch_id'             => $request->appInfo->wx_mch_id,
			'key'                => 'ZU30SEgmNbrmQdFNDR7gZZCF6uHLGDwC',   // API 密钥
			// 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
			// 'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
			// 'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！

			'notify_url'         => url('pay/wx_notify_url'),     // 你也可以在下单时单独设置来想覆盖它
		];

		$app = Factory::payment($config);
		$jssdk = $app->jssdk;

		$payInfo = DB::table('pay')->where('pay_id', $request->input('pay_id'))->first();

		$result = $app->order->unify([
			'body' => '益火吃货-下单',
			'out_trade_no' => $payInfo->pay_id,
			'total_fee' => $payInfo->price,
			// 'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
			'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
			// 'openid' =>  $payInfo->price,
		]);

		$config = $jssdk->sdkConfig($result['prepayId']); // 返回数组

		return $config;
	}

	public function notify_url(Request $request)
	{

		$config = [
			// 必要配置
			'app_id'             => $request->appInfo->wx_appid,
			'mch_id'             => $request->appInfo->wx_mch_id,
			'key'                => 'ZU30SEgmNbrmQdFNDR7gZZCF6uHLGDwC',   // API 密钥
		];

		$app = Factory::payment($config);
		$response = $app->handlePaidNotify(function ($message, $fail) {
			$payInfo = DB::table('notify')->insert(['info' => json_encode($message)]);
			return true;
		});

		return $response;
	}
}
