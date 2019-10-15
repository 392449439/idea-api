<?php

namespace  App\Http\Controllers\Item; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{  // @todo ItemController 这里是要生成的类名字

	public function save(Request $request)
	{
		if ($request->filled('id')) {
			// 保存
			// $request->toArray()

			$result = DB::table('item')
				->where('id', $request->input('id'))
				->update(Arr::except($request->toArray(), ['company_name']));
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加
			$result = DB::table('item')->insert($request->all());
			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	public function linkAd(Request $request)
	{

		// $data = $request->toArray();

		$item_ids = $request->input('item_ids');
		$ad_ids = $request->input('ad_ids');

		$data = [];
		foreach ($item_ids as $key => $item_id) {

			foreach ($ad_ids as $k => $ad) {
				$item = [];
				$item['item_id'] = $item_id;
				$item['ad_id'] = $ad;
				$data[] = $item;
			}
		}

		$result = DB::table('item_ad')->insert($data);
		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function delLinkAd(Request $request)
	{

		$item_ids = $request->input('item_ids');
		$ad_id = $request->input('ad_id');
		foreach ($item_ids as $key => $item_id) {
			$where = [
				['item_id', '=', $item_id],
				['ad_id', '=', $ad_id],
			];
			$result = DB::table('item_ad')->where($where)->delete();
		}
		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function linkGame(Request $request)
	{

		// $data = $request->toArray();

		$item_ids = $request->input('item_ids');
		$game_ids = $request->input('game_ids');

		$data = [];
		foreach ($item_ids as $key => $item_id) {

			foreach ($game_ids as $k => $ad) {
				$item = [];
				$item['item_id'] = $item_id;
				$item['game_id'] = $ad;
				$data[] = $item;
			}
		}

		$result = DB::table('item_game')->insert($data);
		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function delLinkGame(Request $request)
	{

		$item_ids = $request->input('item_ids');
		$game_id = $request->input('game_id');

		foreach ($item_ids as $key => $item_id) {
			$where = [
				['item_id', '=', $item_id],
				['game_id', '=', $game_id],
			];
			$result = DB::table('item_game')->where($where)->delete();
		}
		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function info(Request $request)
	{
		$result =	DB::table('item')
			->where('id', $request->input('id'))
			->first();
		$result->company_name = DB::table('company')->where('id', $result->company_id)->value('name');
		$result->company_state = DB::table('company')->where('id', $result->company_id)->value('state');

		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function list(Request $request)
	{


		$DB = DB::table('item')->orderBy('add_time', 'desc');

		if ($request->filled('ids')) {
			$DB->whereIn('id', explode(',', $request->input('ids')));
		}

		if ($request->filled('state')) {
			$DB->where('state', $request->input('state'));
		}

		if ($request->filled('p')) {
			$DB->where('p', $request->input('p'));
		}
		if ($request->filled('c')) {
			$DB->where('c', $request->input('c'));
		}
		if ($request->filled('a')) {
			$DB->where('a', $request->input('a'));
		}
		if ($request->filled('s')) {
			$DB->where('s', $request->input('s'));
		}

		$company_ids = [];

		$CompanyDB = DB::table('company');
		if ($request->filled('company_name')) {
			$companyList = $CompanyDB
				->where('name', 'like',  '' . $request->input('company_name') . '%')
				->pluck('id');
			if (count($companyList) > 0) {
				$company_ids = $companyList;
			} else {
				return response()->json([
					'code' => 1,
					'msg' => 'success',
					'data' => [],
					'total' => 0,
				]);
			}
		}

		if ($request->filled('company_ids')) {
			$company_ids = explode(',', $request->input('company_ids'));
		}

		if ($request->jwt->company_id) {
			$company_ids = [$request->jwt->company_id];
		}

		if (count($company_ids) > 0) {
			$DB->whereIn('company_id', $company_ids);
		}

		$total = $DB->count() + 0;


		if ($request->filled('page')) {
			$DB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
				->limit($request->input('page_size', 10));
		}

		$DB->select(DB::raw("*,LPAD(id,3,'0') as id,LPAD(company_id,3,'0') as company_id"));

		$result = $DB->get();

		$result->map(function ($v) {
			$v->company_name = DB::table('company')->where('id', $v->company_id)->value('name');
			return $v;
		});

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
			'total' => $total,
		]);
	}
	public function getArea()
	{

		$result = DB::table('item')
			->distinct()
			->get(['p', 'c', 'a']);

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
		]);
	}
	public function getAreaCount(Request $request)
	{

		$DB = DB::table('item');


		if ($request->filled('p')) {
			$DB->where('p', $request->input('p'));
		}

		if ($request->filled('c')) {
			$DB->where('c', $request->input('c'));
		}

		if ($request->filled('a')) {
			$DB->where('a', $request->input('a'));
		}

		$result = $DB->count() + 0;

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
		]);
	}
}
