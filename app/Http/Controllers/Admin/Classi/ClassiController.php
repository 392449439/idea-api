<?php

namespace  App\Http\Controllers\Admin\Classi; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;


class ClassiController extends Controller
{  // @todo AuthController 这里是要生成的类名字

	public function __construct($value = '')
	{ // 这里就是要生成的类文件的通用代码了
		# code...
	}

	public function list(Request $request)
	{


		$DB = DB::table('class')
			->where('domain_id', $request->domain_id)
			->where('data_state', 1)
			->orderBy('add_time', 'desc');

		$DB->where('store_id', $request->input('store_id', ''));

		$result = $DB->get();

		return response()->json([
			'code' => 1,
			'msg' => 'success',
			'data' => $result,
		]);
	}

	public function info(Request $request)
	{

		$result = DB::table('class') //定义表
			->where('id', $request->input('id')) //前台传过来的id
			->first(); //获取数据


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

			$result = DB::table('class')
				->where('id', $request->input('id'))
				->update($data);

			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {


			$result = DB::table('class')->insert($data);

			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}

	public function del(Request $request)
	{

		$result = DB::table('class') //定义表
			->where('id', $request->input('id')) //前台传过来的id
			->delete();

		return [
			'code' => $result ? 1 : -1,
			'msg' => $result ? 'success' : 'error',
			'data' => $result,
		];
	}
}