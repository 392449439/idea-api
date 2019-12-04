<?php

namespace  App\Http\Controllers\Admin\Order; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Dada\Dada;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

	public function list(Request $request)
	{

		$DB = DB::table('order')
			// ->where('store_id', $request->input('store_id'))
			->where('domain_id', $request->domain_id)
			->orderBy('add_time', 'desc');


		if ($request->filled('state')) {
			$DB->where('state', $request->input('state'));
		}

		if ($request->filled('store_id')) {
			$DB->where('store_id', $request->input('store_id'));
		}

		// return  $request->jwt->id;
		$total = $DB->count();

		if ($request->filled('page')) {
			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10));
		}

		if ($request->filled('page_size')) {
			$DB->limit($request->input('page_size', 10));
		}

		$result = $DB->get();

		$result->map(function ($item) {
			/**
			 * 拿到快照数据
			 */
			$item->snapshotInfo = DB::table('snapshot')->where('order_id', $item->order_id)->get();
			if ($item->store_id) {
				$item->storeInfo = DB::table('store')->where('store_id', $item->store_id)->first();
			}

			$item->snapshotInfo =	$item->snapshotInfo->map(function ($el) {
				$el->data = json_decode($el->data, true);
				return $el;
			});
			return $item;
		});


		return [
			'code' => count($result),
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
			'total' => $total * 1,
		];
	}

	public function info(Request $request)
	{

		$DB = DB::table('order')->orderBy('add_time', 'desc');
		$result = $DB->where('order_id', $request->input('order_id'))->first();

		/**
		 * 拿到快照数据
		 */
		$result->snapshotInfo = DB::table('snapshot')->where('order_id', $result->order_id)->get();
		$result->storeInfo = DB::table('store')->where('store_id', $result->store_id)->first();
		$result->payInfo = DB::table('pay')->where('pay_id', $result->pay_id)->first();
		$result->addressInfo = DB::table('order_address')->where('id', $result->address_id)->first();

		$result->snapshotInfo =	$result->snapshotInfo->map(function ($el) {
			$el->data = json_decode($el->data, true);
			return $el;
		});

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	//哒哒下单
	public function pay_order(Request $request)
	{
		$source_id = $request->domainInfo->dada_source_id;
		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => $source_id,
		]);

		$address_id = $request->input('address_id', '1');
		$shop_no = $request->input('store_id');
		$info = $request->input('info');

		if (!$address_id) {
			self::back(-1, '请选择地址');
		}

		$order_address = DB::table('order_address')
			->select('contacts', 'address', 'y', 'x', 'phone')
			->where([
				['id', '=', $address_id]
			])
			->first();

		$order_id = $order_id = 'ORDER' . Carbon::now()->format('YmdHis') . rand(10000, 99999);

		$order_info = [];
		$order_info['shop_no'] = $shop_no;  //门店号
		$order_info['origin_id'] = $order_id;    //第三方订单ID
		$order_info['city_code'] = 021;    //城市的code
		$order_info['cargo_price'] = 0.1;  //订单金额
		$order_info['is_prepay'] = 0;    //是否需要垫付 1:是 0:否 (垫付订单金额，非运费)
		$order_info['receiver_name'] = $order_address->contacts;    //收货人姓名
		$order_info['receiver_address'] = $order_address->address; //收货人地址
		$order_info['callback'] = url('dada/notify'); //回调地址
		$order_info['receiver_lat'] = $order_address->x; //维度
		$order_info['receiver_lng'] = $order_address->y; //经度
		$order_info['receiver_phone'] = $order_address->phone; //收货人手机号
		//        $order_info['receiver_tel'] = ;//收货人电话
		$order_info['tips'] = 0; //小费（单位：元，精确小数点后一位）
		$order_info['info'] = $info; //备注
		$order_info['cargo_type'] = 5;
		//        $order_info['cargo_weight'] = ;订单重量（单位：Kg）
		//        $order_info['cargo_num'] = ;订单商品数量
		//        $order_info['invoice_title'] = ;发票抬头
		//        $order_info['origin_mark'] = ;订单来源标示（该字段可以显示在达达app订单详情页面，只支持字母，最大长度为10）
		//        $order_info['origin_mark_no'] = ;订单来源编号（该字段可以显示在达达app订单详情页面，支持字母和数字，最大长度为30）
		//        $order_info['is_use_insurance'] = ;是否使用保价费
		//        $order_info['is_finish_code_needed'] = ;收货码（0：不需要；1：需要。
		//        $order_info['delay_publish_time'] = ;预约发单时间（预约时间unix时间戳(10位),精确到分;整10分钟为间隔，并且需要至少提前20分钟预约。）
		//        $order_info['is_direct_delivery'] = ;是否选择直拿直送

		//查询订单运费接口
		$dada_http->http('/api/order/queryDeliverFee', $order_info);
		$queryDeliverFee = $dada_http->request();
	}


	public function dadaTest(Request $request)
	{
		$order_id = $request->input('order_id');
		$data_url = $request->input('data_url');

		$source_id = $request->domainInfo->dada_source_id;
		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => $source_id,
		]);

		$dada_http->http($data_url, [
			"order_id" => $order_id
		]);
		$result = $dada_http->request();

		return [$result, $request->all()];
	}

	public function dadaOrderInfo(Request $request)
	{
		$order_id = $request->input('order_id');

		$source_id = $request->domainInfo->dada_source_id;
		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => $source_id,
		]);

		$dada_http->http('/api/order/status/query', [
			"order_id" => $order_id
		]);
		$result = $dada_http->request();

		return [$result];
	}

	public function dadaDadaNotify(Request $request)
	{
		$result = DB::table('dada_notify')->orderBy('add_time', 'desc')->get();
		$result->transform(function ($el) {
			$el->info = json_decode($el->info, true);
			return $el;
		});
		return $result;
	}


	public function dadaOrderTest(Request $request)
	{
		//哒哒
		$source_id = $request->domainInfo->dada_source_id;
		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => $source_id,
		]);

		$order_id = 'ORDER' . Carbon::now()->format('YmdHis') . rand(10000, 99999);

		$order_info = [];
		$order_info['origin_id'] = $order_id;    //第三方订单ID
		$order_info['city_code'] = 021;    //城市的code
		$order_info['cargo_price'] = 0.01;  //订单金额
		$order_info['is_prepay'] = 0;    //是否需要垫付 1:是 0:否 (垫付订单金额，非运费)
		$order_info['receiver_name'] = '孙辉';    //收货人姓名
		$order_info['receiver_address'] = '平高世贸中心商务楼,17楼1711室'; //收货人地址
		$order_info['callback'] = 'http://api.yihuo-cloud.com/dada/notify'; //回调地址
		$order_info['receiver_lat'] = '31.006257'; //维度
		$order_info['receiver_lng'] = '121.235638'; //经度
		$order_info['receiver_phone'] = '18964045539'; //收货人手机号
		$order_info['info'] = 'test'; //备注
		$order_info['cargo_type'] = 5;
		$order_info['shop_no'] = '11047059';

		$dada_http->http('/api/order/addOrder', $order_info);
		$result = $dada_http->request();

		// $result2 = null;
		// if ($result['code'] == 0) {
		// 	$dada_http->http('/api/order/addAfterQuery', [
		// 		'deliveryNo' => $result['result']['deliveryNo'],
		// 	]);
		// 	$result2 = $dada_http->request();
		// }



		return [
			'order_id' => $order_id,
			'result' => $result,
			// 'result2' => $result2,
		];
	}



	private function back($code, $msg, $data = '')
	{
		return [
			'code' => $code,
			'msg' => $msg,
			'data' => $data,
		];
	}
}
