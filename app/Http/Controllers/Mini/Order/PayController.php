<?php

namespace  App\Http\Controllers\Mini\Order; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{


	public function getMini(Request $request)
	{
		Log::info('调用支付');

		$config = [
			// 必要配置
			'app_id'             => 'wx9f4a9bdc95bcc3d7',
			'mch_id'             => '1563112131',
			'key'                => 'ZU30SEgmNbrmQdFNDR7gZZCF6uHLGDwC',   // API 密钥
			'notify_url'         => url('pay/wx_notify_url'),     // 你也可以在下单时单独设置来想覆盖它
		];

		$app = Factory::payment($config);
		$jssdk = $app->jssdk;

		$payInfo = DB::table('pay')->where('pay_id', $request->input('pay_id'))->first();
		$result = $app->order->unify([
			'body' => '益火吃货-下单',
			'out_trade_no' => $payInfo->pay_id,
			'total_fee' => $payInfo->price * 100,
			'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
			'openid' =>  $request->jwt->openid,
		]);
		if ($result['return_code'] != 'SUCCESS') {
			return [
				'code' => -1,
				'data' => $result,
				'msg' => 'error',
			];
		}
		$config = $jssdk->sdkConfig($result['prepay_id']); // 返回数组
		return [
			'code' => 1,
			'data' => $config,
			'msg' => 'success',
		];
	}

	public function notify_url(Request $request)
	{
		$config = [
			'app_id'             => 'wx9f4a9bdc95bcc3d7',
			'mch_id'             => '1563112131',
			'key'                => 'ZU30SEgmNbrmQdFNDR7gZZCF6uHLGDwC',   // API 密钥
		];

		$app = Factory::payment($config);
		$response = $app->handlePaidNotify(function ($message, $fail) {
			$out_trade_no =	$message['out_trade_no'];
			DB::table('pay')->insert(['pay_id' => $out_trade_no])->save(['state' => 2, 'info' => json_encode($message)]);
			DB::table('order')->insert(['pay_id' => $out_trade_no])->save(['state' => 2]);
			return true;
		});
		return $response;
	}
}
