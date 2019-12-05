<?php

namespace  App\Http\Controllers\Admin\Store; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Lib\Dada\Dada;
use Illuminate\Http\Request;
use App\Listeners\Random;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{

    // 门店列表
    public function list(Request $request)
    {

        $DB = DB::table('store') //定义表
            ->where('domain_id', $request->domain_id)
            ->orderBy('add_time', 'desc'); //排序

        $total = $DB->count() + 0;


        if ($request->filled('page')) {
            $DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10));
        }

        if ($request->filled('page_size')) {
            $DB->limit($request->input('page_size', 10));
        }

        if($request->filled('is_up')) {
            $DB->where('is_up',$request->input('is_up'));
        }

        // if ($request->filled('app_id')) {
        // 	$DB->where('app_id', $request->input('app_id'));
        // }


        $result = $DB->get();
        // $result->map(function ($item) {
        // 	$item->label = explode(',', $item->label);
        // 	$item->appInfo = DB::table('app')->where('app_id', $item->app_id)->first();
        // 	return $item;
        // });

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
        $source_id = $request->domainInfo->dada_source_id;

        $result = DB::table('store')
            ->where('store_id', $request->input('store_id'))
            ->first();

        $dada_info = self::checkStore($request->input('store_id'), $source_id);
        $result->dada_info = $dada_info;

        return [
            'code' => $result ? 1 : -1,
            'msg' => $result ? 'success' : 'error',
            'data' => $result,
        ];
    }

    // 保存或者新增
    public function save(Request $request)
    {

        $data = $request->toArray();

        unset($data['dada_info']);
        $data['domain_id'] = $request->domain_id;
        $source_id = $request->domainInfo->dada_source_id;

        if ($request->filled('store_id')) {
            //标签转字符串
            if (Arr::has($data, 'label')) {
                if (gettype($data['label']) == 'array') {
                    $data['label'] = implode(',', $data['label']);
                }
            }
            //是否有哒哒商铺
            $is_dada = DB::table('store')
                ->select('is_dada')
                ->leftJoin('domain', 'store.domain_id', '=', 'domain.domain_id')
                ->where([
                    ['store_id', '=', $data['store_id']]
                ])
                ->first();
            if (!$is_dada) {
                return response()->json([
                    'code' => -1,
                    'msg' => '商铺信息有误',
                    'data' => '',
                ]);
            }
            if ($is_dada->is_dada == 1) {     //已有哒哒商铺
                $update_data = [];
                $update_data['origin_shop_id'] = $data['store_id'];
                $update_data['station_name'] = $data['name'];
                $update_data['city_name'] = $data['c'];
                $update_data['area_name'] = $data['a'];
                $update_data['station_address'] = $data['address'];
                $update_data['lng'] = $data['y'];
                $update_data['lat'] = $data['x'];
                $update_data['contact_name'] = $data['contacts'];
                $update_data['phone'] = $data['phone'];
                //门店是否绑定哒哒
                $check_dada = self::checkStore($update_data['origin_shop_id'], $source_id);
                if ($check_dada['code'] === 0) { //已有门店
                    $update_data['status'] = $data['is_up'];    //门店状态（1-门店激活，0-门店下线）
                    $res = self::saveStore($update_data, $source_id);
                    if ($res['code'] === 0) { //哒哒修改成功
                        $result = DB::table('store')
                            ->where('store_id', $request->input('store_id'))
                            ->update($data);
                        return response()->json([
                            'code' => $result,
                            'msg' => $result == 0 ? '未作修改' : 'success',
                            'data' => '',
                        ]);
                    } else {
                        Log::info('门店更新时创建失败:' . json_encode($res));
                        return response()->json([
                            'code' => -1,
                            'msg' => $res['msg'],
                            'data' => '',
                        ]);
                    }
                } else { //没有门店新增
                    $update_data['business'] = 1;
                    $res = self::addStore([$update_data], $source_id);
                    if ($res['code'] === 0) {
                        unset($data['business'], $data['store_id']);
                        $result = DB::table('store')
                            ->where('store_id', $request->input('store_id'))
                            ->update($data);
                        return response()->json([
                            'code' => $result ? 1 : -1,
                            'msg' => $result ? 'success' : 'error',
                            'data' => '',
                        ]);
                    } else {
                        return response()->json([
                            'code' => -1,
                            'msg' => $res['msg'],
                            'data' => '',
                        ]);
                        Log::info('门店更新失败:' . json_encode($res));
                    }
                }
            }
        } else {    //门店添加
            $random = new Random();
            $data['store_id'] = $random->getRandom(16, 'S_');

            //是否有哒哒店铺
            $is_dada = 1;
            if ($is_dada) {   //有哒哒添加
                $add_data = [];
                $add_data['station_name'] = $data['name'];  //门店名
                $add_data['business'] = 1;                  //业务类型
                $add_data['city_name'] = $data['c'];
                $add_data['area_name'] = $data['a'];
                $add_data['station_address'] = $data['address'];
                $add_data['lng'] = $data['y'];
                $add_data['lat'] = $data['x'];
                $add_data['contact_name'] = $data['contacts'];
                $add_data['phone'] = $data['phone'];
                $add_data['origin_shop_id'] = $data['store_id'];
                $res = self::addStore([$add_data], $source_id);
                if ($res['code'] === 0) {
                    unset($data['business']);
                    $result = DB::table('store')->insert($data);
                    return response()->json([
                        'code' => $result ? 1 : -1,
                        'msg' => $result ? 'success' : 'error',
                        'data' => $result,
                    ]);
                } else {
                    Log::info('门店创建失败:' . json_encode($res));
                    // .$res['result']['failedList'][0]['msg']
                    return response()->json([
                        'code' => -1,
                        'msg' => $res['msg'],
                        'data' => '',
                    ]);
                }
            } else {      //自己添加
                unset($data['business'], $data['username'], $data['password']);
                $result = DB::table('store')->insert($data);
                return response()->json([
                    'code' => $result ? 1 : -1,
                    'msg' => $result ? 'success' : 'error',
                    'data' => '',
                ]);
            }
        }
    }

    // 删除门店接口
    public function del(Request $request)
    {

        $result = DB::table('store')
            ->where('store_id', $request->input('store_id'))
            ->delete();

        return [
            'code' => $result ? 1 : -1,
            'msg' => $result ? 'success' : 'error',
            'data' => $result,
        ];
    }

    //哒哒门店是否存在
    public function checkStore($origin_shop_id, $source_id)
    {

        $dada_http = new Dada([
            "app_key" => env('DADA_APP_KEY'),
            "app_secret" => env('DADA_APP_SECRET'),
            "sandbox" => env('DADA_SANDBOX'),
            "source_id" => $source_id
        ]);

        $dada_http->http('/api/shop/detail', ['origin_shop_id' => $origin_shop_id]);
        return $dada_http->request();
    }

    //门店添加
    public function addStore($store_info, $source_id)
    {

        $dada_http = new Dada([
            "app_key" => env('DADA_APP_KEY'),
            "app_secret" => env('DADA_APP_SECRET'),
            "sandbox" => env('DADA_SANDBOX'),
            "source_id" => $source_id
        ]);

        $dada_http->http('/api/shop/add', $store_info);
        return $dada_http->request();
    }

    //修改门店信息
    public function saveStore($store_info, $source_id)
    {
        $dada_http = new Dada([
            "app_key" => env('DADA_APP_KEY'),
            "app_secret" => env('DADA_APP_SECRET'),
            "sandbox" => env('DADA_SANDBOX'),
            "source_id" => $source_id
        ]);

        $dada_http->http('/api/shop/update', $store_info);
        return $dada_http->request();
    }

    public function dataTotal(Request $request) {

		if($request->filled('store_id')) {

			$price = DB::table('pay')
						->where('store_id', $request->input('store_id'))
						->sum('price');
			
			$order = DB::table('order')
						->where('store_id',$request->input('store_id'))
						->count();
			
			// $user = DB::table('user')
			// 			->where('store_id',$request->input('store_id'))
            // 			->count();
            
            $volume = DB::table('store')
                        ->where('store_id',$request->input('store_id'))
                        ->value('volume');

			return [
				'code' => 1,
				'msg' => 'success',
				'data' => [
					'price' => $price,
                    'order'=>$order,
                    'volume'=>$volume,
				],
				
			];

		}

	}
}
