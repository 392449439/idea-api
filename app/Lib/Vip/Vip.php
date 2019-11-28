<?php

namespace  App\Lib\Vip;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Vip
{

    public function buyTime($user_id, $day)
    {

        if (!$user_id || !$day) return false;


        /**计算截止时间戳 */
        $end_time = Carbon::parse("+$day days")->timestamp;


        /**组成数据 */

        $data = [];
        $data['user_id'] = $user_id;
        $data['end_time'] = $end_time;


        /**没有就添加，有就覆盖 */

        $is = DB::table('vip_user_time')->where('user_id', $user_id)->exists();
        $result = null;
        if ($is) {
            // 保存
            $result = DB::table('vip_user_time')->where('user_id', $user_id)->update($data);
        } else {
            // 添加
            $result =  DB::table('vip_user_time')->insert($data);
        }
        return $result ? true : false;
    }

    public function buyCount($user_id, $day, $max)
    {
        if (!$user_id || !$day || !$max) return false;

        /**计算截止时间戳 */
        $end_time = Carbon::parse("+$day days")->timestamp;

        /**组成数据 */

        $data = [];
        $data['user_id'] = $user_id;
        $data['end_time'] = $end_time;
        $data['max'] = $max;


        /**没有就添加，有就覆盖 */

        // exists
        $is = DB::table('vip_user_count')->where('user_id', $user_id)->exists();
        $result = null;
        if ($is) {
            // 保存
            $result = DB::table('vip_user_count')->where('user_id', $user_id)->update($data);
        } else {
            // 添加
            $result =  DB::table('vip_user_count')->insert($data);
        }
        return $result ? true : false;
    }
}
