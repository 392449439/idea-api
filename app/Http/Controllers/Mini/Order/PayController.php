<?php

namespace  App\Http\Controllers\Mini\Order; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Printer\Printer;
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

		$app_id = $request->appInfo->app_id;
		$config = [
			// 必要配置
			'app_id'             => $request->appInfo->wx_appid,
			'mch_id'             => $request->appInfo->wx_mch_id,
			'key'                => $request->appInfo->wx_mch_secret,   // API 密钥
			'notify_url'         => url("pay/wx_notify_url/{$app_id}"),     // 你也可以在下单时单独设置来想覆盖它
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

	public function notify_url(String $app_id)
	{

		dump($app_id);
		die;
		$App = DB::table('app')->where('app_id', $app_id)->first();

		$config = [
			'app_id'             => $App->wx_appid,
			'mch_id'             => $App->wx_mch_id,
			'key'                => $App->wx_mch_secret,
		];

		$app = Factory::payment($config);
		$response = $app->handlePaidNotify(function ($message, $fail) {
			$out_trade_no =	$message['out_trade_no'];

			DB::table('pay')
				->where('pay_id', $out_trade_no)
				->update([
					'state' => 2,
					'info' => json_encode($message)
				]);
			DB::table('order')
				->where('pay_id', $out_trade_no)
				->update(['state' => 2]);

			/**
			 * 支付成功后打印订单
			 */
			PayController::printOrder($out_trade_no);

			return true;
		});
		return $response;
	}

	private static function printOrder($pay_id)
	{
		$order_id = DB::table('order')
			->where('pay_id', $pay_id)
			->value('order_id');

		$snapshotInfo = DB::table('snapshot')
			->where('order_id', $order_id)
			->get();

		$data = $snapshotInfo->map(function ($item) {
			$item->data = json_decode($item->data, true);
			$newItem = [
				'title' => $item->data['title'],
				'price' => $item->data['price'],
				'num' =>  $item->data['quantity'],
			];
			return $newItem;
		});

		$printer = new Printer();
		$res = $printer->printData($data, '921510805');
		return ["data" => $res];

		// if ($res) {
		// 	return response()->json([
		// 		'code' => 1,
		// 		'msg' => 'success',
		// 		'data' => $res,
		// 	]);
		// } else {
		// 	return response()->json([
		// 		'code' => -1,
		// 		'msg' => 'error',
		// 		'data' => null,
		// 	]);
		// }
	}
}
