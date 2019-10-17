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
			$result = DB::table('ad')
				->where('id', $request->input('id'))
				->update($request->all());
			return response()->json([
				'code' => $result >= 0 ? 1 : -1,
				'msg' =>  $result >= 0 ? 'success' : 'error',
				'data' => $result,
			]);
		} else {
			$data = $request->toArray();
			$result = DB::table('ad')->insertGetId($data);
			return response()->json([
				'code' => $result ? 1 : -1,
				'msg' => $result ? 'success' : 'error',
				'data' => $result,
			]);
		}
	}
}
