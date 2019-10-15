<?php

namespace  App\Http\Controllers\Company; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{  // @todo CompanyController 这里是要生成的类名字


	public function save(Request $request)
	{
		if ($request->filled('id')) {
			// 保存
			// $request->toArray()

			$result = DB::table('company')
				->where('id', $request->input('id'))
				->update(Arr::except($request->toArray(), ['item_total']));
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			// 添加
			$result = DB::table('company')->insert($request->all());
			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	public function info(Request $request)
	{
		$result =	DB::table('company')
			->where('id', $request->input('id'))
			->first();
		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
		]);
	}

	public function del(Request $request)
	{
		$result =	DB::table('company')
			->where('id', $request->input('id'))
			->delete();

		/**
		 * 删掉设备
		 */

		DB::table('item')
			->where('company_id', $request->input('id'))
			->delete();

		return response()->json([
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		]);
	}

	public function list(Request $request)
	{


		$CompanyDB = DB::table('company')->orderBy('add_time', 'desc');

		if ($request->filled('p')) {
			$CompanyDB->where('p', $request->input('p'));
		}

		if ($request->filled('c')) {
			$CompanyDB->where('c', $request->input('c'));
		}

		if ($request->filled('a')) {
			$CompanyDB->where('a', $request->input('a'));
		}

		if ($request->filled('id')) {
			$CompanyDB->where('id', $request->input('id'));
		}

		if ($request->filled('name')) {
			$CompanyDB->where('name', 'like',  '' . $request->input('name') . '%');
		}

		if ($request->filled('state')) {
			$CompanyDB->where('state', $request->input('state'));
		}
		$total = $CompanyDB->count() + 0;

		$CompanyDB->offset(($request->input('page', 1) - 1) * $request->input('page_size', 10))
			->limit($request->input('page_size', 10));


		$CompanyDB->select(DB::raw("*,LPAD(id,3,'0') as id"));

		$result = $CompanyDB->get();

		foreach ($result as $k => $v) {
			$v->item_total = DB::table('item')->where('company_id', $v->id)->count();
		}

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
			'total' => $total,
		]);
	}
	public function getArea()
	{

		$result = DB::table('company')
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

		$DB = DB::table('company');


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
