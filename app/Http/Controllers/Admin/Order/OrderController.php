<?php

namespace  App\Http\Controllers\Admin\Order; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

	public function list(Request $request)
	{

		$DB = DB::table('order')
			->where('store_id', $request->input('store_id'))
			->where('domain_id', $request->domain_id)
			->orderBy('add_time', 'desc');


		if ($request->filled('state')) {
			$DB->where('state', $request->input('state'));
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
}
