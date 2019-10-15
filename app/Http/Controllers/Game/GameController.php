<?php

namespace  App\Http\Controllers\Game; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{  // @todo UserController 这里是要生成的类名字

	public function __construct($value = '')
	{ // 这里就是要生成的类文件的通用代码了
		# code...
	}
	public function save(Request $request)
	{

		if ($request->filled('id')) {
			$result = DB::table('game')
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
			$data['company_id'] = $request->jwt->company_id;
			$result = DB::table('game')->insert($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
			return [
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			];
		}
	}
	public function list(Request $request)
	{


		if ($request->filled('item_ids')) {

			$DB = DB::table('item_game')
				->groupBy('game_id')
				->whereIn('item_id', explode(',', $request->input('item_ids')));

			$result = $DB->get('game_id');

			$result->transform(function ($v) {
				return $v->game_id;
			});

			$DB = DB::table('game')->orderBy('add_time', 'desc')
				->whereIn('id', $result);

			$result = $DB->get();

			return response()->json([
				'code' => 1,
				'msg' => 'success',
				'data' => $result,
			]);
		}

		$DB = DB::table('game')->orderBy('add_time', 'desc');


		if ($request->jwt->company_id) {
			$company_ids = [$request->jwt->company_id];
			$DB->whereIn('company_id', $company_ids);
		}


		if ($request->filled('name')) {
			$DB->where('name', 'like',  '%' . $request->input('name') . '%');
		}

		if ($request->filled('is_up')) {
			$DB->where('is_up', $request->input('is_up'));
		}


		$total = $DB->count() + 0;

		if ($request->filled('page')) {

			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
				->limit($request->input('page_size', 10));
		}


		// $DB->select(DB::raw("*,LPAD(id,3,'0') as id"));

		$result = $DB->get();

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
			'total' => $total,
		]);
	}
	public function info(Request $request)
	{
		$result =	DB::table('game')
			->where('id', $request->input('id'))
			->first();
		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}
}
