<?php

namespace  App\Http\Controllers\Notice; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class NoticeController extends Controller
{  // @todo ItemController 这里是要生成的类名字

	public function save(Request $request)
	{
		if ($request->filled('data')) {
			$item_ids = $request->input('item_ids');

			DB::table('notice')
				->whereIn('item_id', 	$item_ids)
				->delete();


			$notice = $request->input('data');
			$data = [];

			foreach ($item_ids  as $key => $value) {

				foreach ($notice as $k => $v) {
					$item = $v;
					$item['item_id'] = $value;
					$item['state'] = 1;
					$data[] = $item;
				}
			}
			$result = DB::table('notice')->insert($data);
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			$result = DB::table('notice')
				->where('id', $request->input('id'))
				->update($request->all());

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}



	public function info(Request $request)
	{
		$result =	DB::table('notice')
			->orderBy('sort', 'asc')
			->where('item_id', $request->input('item_ids')[0])
			->get();

		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function list(Request $request)
	{


		$DB = DB::table('notice')
			->orderBy("item_id", 'asc')
			->orderBy('sort', 'asc');

		if ($request->filled('ids')) {
			$DB->whereIn('id',  $request->input('ids'));
		}

		if ($request->filled('item_ids')) {
			$DB->whereIn('item_id',  $request->input('item_ids'));
		}

		if ($request->filled('state')) {
			if (is_array($request->input('state'))) {
				$DB->whereIn('state', $request->input('state'));
			} else {
				$DB->where('state', $request->input('state'));
			}
		}

		$total = $DB->count() + 0;

		if ($request->filled('page')) {
			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
				->limit($request->input('page_size', 10));
		}


		$result = $DB->get();

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
			'total' => $total,
		]);
	}
}
