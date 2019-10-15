<?php

namespace  App\Http\Controllers\Ad; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AdController extends Controller
{  // @todo UserController 这里是要生成的类名字

	public function __construct($value = '')
	{ // 这里就是要生成的类文件的通用代码了
		# code...
	}
	public function save(Request $request)
	{
		// return response()->json($request->all());

		if ($request->filled('id')) {
			$result = DB::table('ad')
				->where('id', $request->input('id'))
				->update(Arr::except($request->toArray(), ['company_name']));

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加
			// $item_ids =	$request->input('item_ids');
			// $data = [];
			// foreach ($item_ids as $key => $value) {
			// 	$item = Arr::except($request->toArray(), ['item_ids']);
			// 	$item['item_id'] = $value;
			// 	$data[] = $item;
			// }

			$data = $request->toArray();
			$data['company_id'] = $request->jwt->company_id;
			$result = DB::table('ad')->insertGetId($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}
	public function list(Request $request)
	{
		// return $request->all();
		if ($request->filled('item_ids')) {

			$DB = DB::table('item_ad')
				->groupBy('ad_id')
				// ->orderBy('add_time', 'desc')
				->whereIn('item_id', explode(',', $request->input('item_ids')));

			$result = $DB->get('ad_id');

			$result->transform(function ($v) {
				return $v->ad_id;
			});

			$DB = DB::table('ad')->orderBy('add_time', 'desc')
				->whereIn('id', $result);

			$result = $DB->get();

			return response()->json([
				'code' => 1,
				'msg' => 'success',
				'data' => $result,
			]);
		}

		$DB = DB::table('ad')->orderBy('add_time', 'desc');


		if ($request->jwt->company_id) {
			$company_ids = [$request->jwt->company_id];
			$DB->whereIn('company_id', $company_ids);
		}

		if ($request->filled('title')) {
			$DB->where('title', 'like',  '%' . $request->input('title') . '%');
		}

		if ($request->filled('state')) {
			if (is_array($request->input('state'))) {
				$DB->whereIn('state', $request->input('state'));
			} else {
				$DB->where('state', $request->input('state'));
			}
		}
		if ($request->filled('is_up')) {
			$DB->where('is_up', $request->input('is_up'));
		}
		if ($request->filled('type')) {
			$DB->where('type', $request->input('type'));
		}

		$total = $DB->count() + 0;

		if ($request->filled('page')) {
			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
				->limit($request->input('page_size', 10));
		}

		// $DB->select(DB::raw("*,LPAD(id,3,'0') as id"));


		$result = $DB->get();

		$result->map(function ($v) {
			if ($v->company_id) {
				$v->company_name = DB::table('company')->where('id', $v->company_id)->value('name');
			}
			return $v;
		});

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
			'total' => $total,
		]);
	}

	public function info(Request $request)
	{
		$result =	DB::table('ad')
			->where('id', $request->input('id'))
			->first();
		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}
}
