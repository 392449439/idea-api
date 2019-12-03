<?php

namespace  App\Http\Controllers\Mini\Order; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Dada\Dada;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
	public function create(Request $request)
	{

		/**
		 * 组成模型
		 */
		$SnapshotDB = DB::table('snapshot');
		$AddressDB = DB::table('address');
		$OrderAddressDB = DB::table('order_address');
		$OrderDB = DB::table('order');
		$PayDB = DB::table('pay');

		// $SnapshotDB->delete();
		// $OrderAddressDB->delete();
		// $OrderDB->delete();
		// $PayDB->delete();




		$order_id = 'ORDER' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
		$pay_id = 'PAY' . Carbon::now()->format('YmdHis') . rand(10000, 99999);


		$goodsArr = $request->input('goods'); //商品
		$store_id = ''; //店铺id
		$remarks = $request->input('remarks'); // 用户的备注
		$address_id = $request->input('address_id'); // 用户选择的地址，不能直接使用，需要拿出来备份

		if ($request->filled('store_id')) {
			$store_id = $request->input('store_id');
		}

		/**
		 * 拿出来收货地址
		 */

		$addressInfo = $AddressDB->where('id', $address_id)->first();
		$addressInfo = collect($addressInfo)->except(['id', 'edit_time', 'date_state']);

		/**
		 * 组成快照
		 */

		// $goodsData = [];
		$snapshotInfoArr = [];
		$price = 0.00;

		foreach ($goodsArr as $goods) {
			$goodsInfo = DB::table('goods')->where('id', $goods['id'])->first();
			$goodsInfo = collect($goodsInfo)->except(['stock', 'edit_time', 'date_state']);


			$price += $goodsInfo['price'] * $goods['quantity'];

			$snapshotInfo = [];
			$snapshotInfo['goods_id'] =  $goodsInfo['id'];
			$snapshotInfo['order_id'] = $order_id;
			$snapshotInfo['user_id'] = '';
			$snapshotInfo['store_id'] = $store_id;
			$snapshotInfo['domain_id'] = $request->domainInfo->domain_id;
			$snapshotInfo['type'] = 'pay_order';
			$snapshotInfo['title'] =  $goodsInfo['title'];
			$snapshotInfo['data'] =	collect([$goods, $goodsInfo])->collapse()->toJson();

			$snapshotInfoArr[] = $snapshotInfo;
		}

		/**
		 * 组成订单
		 */

		//哒哒
        $dada_http = new Dada([
            "app_key" => env('DADA_APP_KEY'),
            "app_secret" => env('DADA_APP_SECRET'),
            "sandbox" => env('DADA_SANDBOX'),
            "source_id" => '73753',
        ]);

        $order_info = [];
        $order_info['shop_no'] = $store_id;  //门店号
        $order_info['origin_id'] = $order_id;    //第三方订单ID
        $order_info['city_code'] = 021;    //城市的code
        $order_info['cargo_price'] = $price;  //订单金额
        $order_info['is_prepay'] = 0;    //是否需要垫付 1:是 0:否 (垫付订单金额，非运费)
        $order_info['receiver_name'] = $addressInfo['contacts'];    //收货人姓名
        $order_info['receiver_address'] = $addressInfo['address']; //收货人地址
        $order_info['callback'] = url(); //回调地址
        $order_info['receiver_lat'] = $addressInfo['x']; //维度
        $order_info['receiver_lng'] = $addressInfo['y']; //经度
        $order_info['receiver_phone'] = $addressInfo['phone'];//收货人手机号
//        $order_info['receiver_tel'] = ;//收货人电话
        $order_info['tips'] = 0;//小费（单位：元，精确小数点后一位）
        $order_info['info'] = $remarks;//备注
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
        $dada_http->http('/api/order/queryDeliverFee',$order_info);
        $queryDeliverFee = $dada_http->request();

        Log::info('查询运费:'.json_encode($queryDeliverFee));
        if($queryDeliverFee['code'] !== 0){
            return [
                'code' => -1,
                'msg' => $queryDeliverFee['msg'],
                'data' => '',
            ];
        }


		$orderInfo = [];
		$orderInfo['freight_price'] = $queryDeliverFee['result']['fee'];

		$orderInfo['order_id'] = $order_id;
		$orderInfo['pay_id'] = $pay_id;
		$orderInfo['user_id'] = $request->jwt->id;
		$orderInfo['store_id'] = $store_id;
		$orderInfo['domain_id'] = $request->domainInfo->domain_id;
		$orderInfo['price'] = $price;
		$orderInfo['remarks'] = $remarks;
		$address_id = $OrderAddressDB->insertGetId($addressInfo->toArray());

		$orderInfo['address_id'] = $address_id;
		$orderInfo['delivery_no'] = $queryDeliverFee['result']['deliveryNo'];



		/**
		 * 组成支付单数据
		 */

		$payInfo = [];
		$payInfo['pay_id'] = $pay_id;
//		$payInfo['price'] = $price+$queryDeliverFee['result']['fee'];
		$payInfo['price'] = 0.01;
		$payInfo['domain_id'] = $request->domainInfo->domain_id;


		$SnapshotDB->insert($snapshotInfoArr);
		$PayDB->insert($payInfo);
		$OrderDB->insert($orderInfo);


		return [
			'code' => 1,
			'msg' => 'success',
			'data' => [
				"pay_id" => $pay_id,
				"order_id" => $order_id
			],
		];
	}


	public function list(Request $request)
	{

		$DB = DB::table('order')->orderBy('add_time', 'desc');

		$DB->where('user_id', $request->jwt->id);
		$DB->where('domain_id', $request->domainInfo->domain_id);

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
}
