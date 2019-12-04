<?php

namespace  App\Http\Controllers\Admin\Domain; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Dada\Dada;
use Illuminate\Http\Request;
use App\Listeners\Random;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DomainController extends Controller
{

	// 门店列表
	public function list(Request $request)
	{

		$DB = DB::table('domain') //定义表
			->orderBy('add_time', 'desc'); //排序

		$total = $DB->count() + 0;

		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));

		$result = $DB->get();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
			'total' => $total,
		];
	}

	// 门店详情
	public function info(Request $request)
	{

		$result = DB::table('domain')
			->where('domain_id', $request->input('domain_id'))
			->first();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	// 保存或者新增
	public function save(Request $request)
	{

		if ($request->filled('domain_id')) {

			$result = DB::table('domain')
				->where('domain_id', $request->input('domain_id'))
				->update($request->all());

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {

			$data = $request->toArray();

			$random = new Random();

			$data['domain_id'] = $random->getRandom(16, 'D_');

			$result = DB::table('domain')->insert($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	// 删除门店接口
	public function del(Request $request)
	{

		$result = DB::table('domain')
			->where('domain_id', $request->input('domain_id'))
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	//注册商户
	public function addDada(Request $request)
	{
		$domain_id = $request->domain_id;

		$data = [];
		$data['mobile'] = $request->input('mobile');
		$data['city_name'] = $request->input('city_name');
		$data['enterprise_name'] = $request->input('enterprise_name');
		$data['enterprise_address'] = $request->input('enterprise_address');
		$data['contact_name'] = $request->input('contact_name');
		$data['contact_phone'] = $request->input('contact_phone');
		$data['email'] = $request->input('email');

		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => '',
		]);
		$dada_http->http('/merchantApi/merchant/add', $data);

		$res = $dada_http->request();

		Log::info('达达注册商户返回：' . json_encode($res));
		//哒哒创建商铺成功，入库
		if ($res['code'] === 0) {
			$update = [];
			$update['is_dada'] = 1;
			$update['dada_source_id'] = $res['result'];
			$result = DB::table('domain')
				->where([
					['domain_id', '=', $domain_id],
				])
				->update($update);

			if (!$result) {
				Log::info('商户入库失败：' . json_encode($res) . '--domain_id--' . $domain_id);
			}
		}

		return [
			'code' => $res['code'] === 0 ? 1 : -1,
			'msg' => $res['code'] === 0 ? 'success' : 'error',
			'data' => $res['msg'],
		];
	}

	//哒哒余额
	public function dadaBalance(Request $request)
	{
		$source_id = $request->domainInfo->dada_source_id;
		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => $source_id,
		]);
		$data['category'] = 3;
		$dada_http->http('/api/balance/query', $data);
		$res = $dada_http->request();
		if ($res['code'] == 0) {
			return [
				'code' => 1,
				'msg' => 'success',
				'data' => $res['result'],
			];
		} else {
			return [
				'code' => -1,
				'msg' => $res['msg'],
				'data' => '',
			];
		}
	}

	//哒哒支付
	public function dadaPay(Request $request)
	{
		$source_id = $request->domainInfo->dada_source_id;
		$dada_http = new Dada([
			"app_key" => env('DADA_APP_KEY'),
			"app_secret" => env('DADA_APP_SECRET'),
			"sandbox" => env('DADA_SANDBOX'),
			"source_id" => $source_id,
		]);

		$data['amount'] = $request->input('money');
		$data['category'] = 'PC';
		$data['notify_url'] = $request->input('notify_url', '');

		$dada_http->http('/api/recharge', $data);
		$res = $dada_http->request();
		if ($res['code'] === 0) {
			return [
				'code' => 1,
				'msg' => $res['msg'],
				'data' => [
					'result' => $res['result']
				],
			];
		} else {
			return [
				'code' => -1,
				'msg' => $res['msg'],
				'data' => '',
			];
		}
	}

	public function dataTotal(Request $request) {

		$price = DB::table('pay')
					->where('domain_id', $request->domain_id)
					->sum('price');
		
		$order = DB::table('order')
					->where('domain_id',$request->domain_id)
					->count();
		
		$user = DB::table('user')
					->where('domain_id',$request->domain_id)
					->count();

		return [
			'code' => 1,
			'msg' => 'success',
			'data' => [
				'price' => $price,
				'order'=>$order,
				'user'=>$user,
			],
			
		];

	}

}
