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

		$App = DB::table('app')->where('app_id', $app_id)->first();

		$config = [
			'app_id'             => $App->wx_appid,
			'mch_id'             => $App->wx_mch_id,
			'key'                => $App->wx_mch_secret,
		];

		$app = Factory::payment($config);
		$response = $app->handlePaidNotify(function ($message, $fail) {
			$out_trade_no =	$message['out_trade_no'];

			$pay = DB::table('pay')
				->where('pay_id', $out_trade_no)
				->first();
			if ($pay->state != 2) {
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
			}

			return true;
		});
		return $response;
	}

	public function vip_notify_url(String $app_id)
	{

		$App = DB::table('app')->where('app_id', $app_id)->first();

		$config = [
			'app_id'             => $App->wx_appid,
			'mch_id'             => $App->wx_mch_id,
			'key'                => $App->wx_mch_secret,
		];

		$app = Factory::payment($config);
		$response = $app->handlePaidNotify(function ($message, $fail) {
			$out_trade_no =	$message['out_trade_no'];

			$pay = DB::table('pay')
				->where('pay_id', $out_trade_no)
				->first();
			if ($pay->state != 2) {
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
			}

			return true;
		});
		return $response;
	}



	private static function printOrder($pay_id)
	{
		$pay = DB::table('pay')
			->where('pay_id', $pay_id)
			->first();

		$order = DB::table('order')
			->where('pay_id', $pay_id)
			->first();

		$store = DB::table('store')
			->where('store_id', $order->store_id)
			->first();

		$orderAddress = DB::table('order_address')
			->where('id',  $order->address_id)
			->first();

		$snapshotInfo = DB::table('snapshot')
			->where('order_id', $order->order_id)
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

		$header = [
			"<CB>" . $store->name . "</CB><BR>",
			'名称           单价  数量 金额<BR>',
			'--------------------------------<BR>',
		];

		$footer = [
			'--------------------------------<BR>',
			'订单号：' . $order->order_id,
			'支付号：' . $pay->pay_id,
			'合计：' . number_format($pay->price, 2) . '元<BR>',
			'送货地点：' . $orderAddress->address . '<BR>',
			'联系电话：' .    $orderAddress->phone,
			'联系人：' .    $orderAddress->contacts,
			'订餐时间：' . $order->add_time,
			'备注：' . ($order->remarks ? $order->remarks : '无') . '<BR><BR>',
			'<QR>https://www.yihuo-cloud.com/</QR>',
		];


		$res = $printer->printData($header, $data, $footer, '921510805');
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
