<?php

namespace  App\Http\Controllers\Admin\Goods; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Listeners\Random;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{

	// 门店列表
	public function list(Request $request)
	{

		$DB = DB::table('goods') //定义表
			->where('domain_id', $request->domain_id)
			->orderBy('add_time', 'desc'); //排序

		$total = $DB->count() + 0;

		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));

		if ($request->filled('class_id')) {
			$DB->where('class_id', $request->input('class_id'));
		}
		if ($request->filled('store_id')) {
			$DB->where('store_id', $request->input('store_id'));
		}
		if ($request->filled('is_up')) {
			$DB->where('is_up', $request->input('is_up'));
		}

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

		$result = DB::table('goods')
			->where('id', $request->input('id'))
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

		$data = $request->toArray();
		$data['domain_id'] = $request->domain_id;

		if ($request->filled('id')) {

			$result = DB::table('goods')
				->where('id', $request->input('id'))
				->update($data);

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {

			$result = DB::table('goods')->insert($data);

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

		$result = DB::table('goods')
			->where('id', $request->input('id'))
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}

	public function volume(Request $request)
	{
		$store_id = $request->input('store_id');

		$result = DB::select("

			SELECT 
				goods_id,
				goods.title,
				goods.goods_head,
				COUNT( snapshot.goods_id ) AS sales_volume
			FROM
				snapshot
			LEFT JOIN goods ON snapshot.goods_id = goods.id
			WHERE snapshot.store_id  = '$store_id'
			GROUP BY
				snapshot.goods_id
			ORDER BY
				sales_volume DESC
			LIMIT 1,10

		");

		return $result;
	}
}
