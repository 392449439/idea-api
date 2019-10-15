<?php

namespace  App\Http\Controllers\Repair; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{  // @todo UserController 这里是要生成的类名字

	public function __construct($value = '')
	{ // 这里就是要生成的类文件的通用代码了
		# code...
	}
	public function save(Request $request)
	{

		if ($request->filled('id')) {
			$result = DB::table('repair')
				->where('id', $request->input('id'))
				->update($request->all());
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加

			$data = $request->toArray();
			$data['img_url'] = json_encode($data['img_url']);
			$data['audio_url'] = json_encode($data['audio_url']);

			$result = DB::table('repair')->insert($data);
			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}
	public function list(Request $request)
	{


		$DB = DB::table('repair')
			->join('item', 'repair.item_id', '=', 'item.id')
			->join('company', 'item.company_id', '=', 'company.id')
			->select('repair.*', 'repair.id as repair_id', 'item.*', 'repair.add_time as repair_add_time', 'repair.state as repair_state', 'company.name as company_name')
			// ->select(DB::raw('table1.id'))
			->orderBy('repair.add_time', 'desc');

		if ($request->filled('ids')) {
			$DB->whereIn('repair.item_id', explode(',', $request->input('ids')));
		}

		if ($request->filled('state')) {
			$DB->where('repair.state', $request->input('state'));
		}

		if ($request->filled('company_name')) {
			$DB->where('company.name', 'like',  '%' . $request->input('company_name') . '%');
		}

		$total = $DB->count() + 0;

		$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));

		// $DB->select(DB::raw("*,LPAD(id,3,'0') as id"));

		$result = $DB->get();

		// foreach ($result as $k => $v) {
		// $v->item_info = DB::table('item')->where('id', $v->item_id)->select(DB::raw("*,LPAD(id,3,'0') as id"))->first();
		// $v->company_info = DB::table('company')->where('id', $v->item_info->company_id)->select(DB::raw("*,LPAD(id,3,'0') as id"))->first();
		// }



		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
			'total' => $total,
		]);
	}

	public function info(Request $request)
	{
		$result =	DB::table('repair')
			->where('id', $request->input('id'))
			->select(DB::raw("*,LPAD(item_id,3,'0') as item_id"))
			->first();
		if ($result) {
			$result->img_url = json_decode($result->img_url);
			$result->audio_url = json_decode($result->audio_url);

			/**
			 * 查设备信息
			 */

			$result->item_info = DB::table('item')
				->where('id', $result->item_id)
				->select(DB::raw("*,LPAD(id,3,'0') as id"))
				->first();

			$result->company_info = DB::table('company')
				->where('id', $result->item_info->company_id)
				->select(DB::raw("*,LPAD(id,3,'0') as id"))
				->first();

			/**
			 * 查公司信息
			 */



			return response()->json([
				'code' => 1,
				'msg' => 'success',
				'data' => $result,
			]);
		} else {
			return response()->json([
				'code' => -1,
				'msg' => '未找到报修信息！',
				'data' => $result,
			]);
		}
	}

	// getCompany
	public function getCompany(Request $request)
	{

		$ids = DB::table('repair')
			->join('item', 'repair.item_id', '=', 'item.id')
			->join('company', 'item.company_id', '=', 'company.id')
			->select('repair.*', 'repair.id as repair_id', 'item.*',  'repair.state as repair_state', 'company.id as company_id')
			->orderBy('repair.add_time', 'desc')
			->get();




		$ids = collect($ids)
			->map(function ($item) {
				return $item->company_id;
			})
			->unique()
			->values()
			->all();

		$ddd =		[
			["company_id" => 1],
			["company_id" => 1],
			["company_id" => 1],
		];

		$ddd =		[
			1, 2, 3
		];

		$DB = DB::table('company')
			->whereIn('id', $ids);

		$result = $DB->get();

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result ? $result : [],
		]);
	}
}
