<?php

namespace  App\Http\Controllers\Feedback; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{

	public function save(Request $request)
	{

		if ($request->filled('id')) {
			$result = DB::table('feedback')
				->where('id',  $request->input('id'))
				->update($request->all());
			return [
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			];
		} else {
			$result = DB::table('feedback')->insert($request->all());
			return [
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			];
		}
	}
}
