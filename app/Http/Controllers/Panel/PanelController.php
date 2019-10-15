<?php

namespace  App\Http\Controllers\Panel; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{

	public function packet(Request $request)
	{
		// $result =	DB::table('item')
		// 	->where('id', $request->input('id'))
		// 	->first();
		// $result->company_name = DB::table('company')->where('id', $result->company_id)->value('name');

		$item_id = $request->input('item_id');

		/**
		 * 判断逻辑：
		 * 
		 * 开始日期小于当前的日期
		 * 且
		 * 结束日期大于当前的日期
		 * 
		 * 且
		 * 
		 * 开始时间小于当前的时间
		 * 且
		 * 结束时间大于当前的时间
		 * 
		 */
		// 获取广告信息
		$adId = DB::table('item_ad')
			->where('item_id', '=', $item_id)
			->pluck('ad_id');

		$adList = DB::table('ad')
			->whereIn('id', $adId)
			->where('start_date', '<=', Carbon::now()->toDateString())
			->where('end_date', '>=', Carbon::now()->toDateString())
			->where('start_time', '<=', Carbon::now()->toTimeString())
			->where('end_time', '>=', Carbon::now()->toTimeString())
			->where('is_up', 1)
			->where('state', 2)
			->get();

		// 获取公告信息

		$noticeList = DB::table('notice')
			->orderBy('sort')
			->where('item_id', '=', $item_id)
			->where('start_date', '<=', Carbon::now()->toDateString())
			->where('end_date', '>=', Carbon::now()->toDateString())
			->where('start_time', '<=', Carbon::now()->toTimeString())
			->where('end_time', '>=', Carbon::now()->toTimeString())
			->get();

		// 获取游戏信息

		$gameId = DB::table('item_game')
			->where('item_id', '=', $item_id)
			->pluck('game_id');

		$gameList = DB::table('game')
			->whereIn('id', $gameId)
			->where('start_date', '<=', Carbon::now()->toDateString())
			->where('end_date', '>=', Carbon::now()->toDateString())
			->where('is_up', 1)
			->get();


		$data = [
			"adList" => $adList,
			"gameList" => $gameList,
			"noticeList" => $noticeList
		];


		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $data,
		]);
	}
}
