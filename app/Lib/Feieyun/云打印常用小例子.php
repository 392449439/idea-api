<?php
    //飞鹅技术支持-2019-09-17 更新

    /**************************************************************************************************

    /**
     * 参考例子 1
     * [循环调用接口，实现多台打印机同时出单打印] 例如前台打印机和后厨打印机同时出单
     */
    $content = '<CB>测试打印</CB><BR>';
    $content .= '名称　　　　　 单价  数量 金额<BR>';
    $content .= '--------------------------------<BR>';
    $content .= '饭　　　　　 　10.0   10  100.0<BR>';
    $content .= '炒饭　　　　　 10.0   10  100.0<BR>';
    $content .= '蛋炒饭　　　　 10.0   10  100.0<BR>';
    $content .= '鸡蛋炒饭　　　 10.0   10  100.0<BR>';
    $content .= '西红柿炒饭　　 10.0   10  100.0<BR>';
    $content .= '西红柿蛋炒饭　 10.0   10  100.0<BR>';
    $content .= '西红柿鸡蛋炒饭 10.0   10  100.0<BR>';
    $content .= '--------------------------------<BR>';
    $content .= '备注：加辣<BR>';
    $content .= '合计：xx.0元<BR>';
    $content .= '送货地点：广州市南沙区xx路xx号<BR>';
    $content .= '联系电话：13888888888888<BR>';
    $content .= '订餐时间：2014-08-08 08:08:08<BR>';
    $content .= '<QR>http://www.feieyun.com</QR>';//把二维码字符串用标签套上即可自动生成二维码

    $arr = array('123456788','123456789');//查询出来有打印机编号1，打印机编号2 ...
    foreach ($arr as $key => $value) {//循环发给多个打印机打印
      wp_print($value,$content,1);//调用云打印接口
    }

    /**************************************************************************************************

    /**
     * 参考例子 2
     * [分单打印，把订单上菜品切一刀，实现每个菜品生成一个小单]
     */
    $time = date('Y-m-d H:i:s',time());
    $arr = array('酸菜鱼','可乐鸡翅','椒盐虾' );
    $content = '';
    $num = count($arr);
    foreach ($arr as $key => $value) {
      $content .= '<CB>桌号01</CB><BR>';
      $content .= '<C><L>'.$value.'</L></C><BR>';
      $content .= '时间：'.$time.'<BR>';
      $end = array_keys($arr);
      if(end($end)==$key){
          break;
      }else{
          $content .= '<BR><BR><BR><CUT>';//控制切纸
      }
    }
    wp_print(SN,$content,1);//调用云打印接口

    /**************************************************************************************************

    /**
     * 参考例子 3
     * [统计字符串字节数补空格，实现左右排版对齐]
     * @param  [string] $str_left    [左边字符串]
     * @param  [string] $str_right   [右边字符串]
     * @param  [int]    $length      [输入当前纸张规格一行所支持的最大字母数量]
     *                               58mm的机器,一行打印16个汉字,32个字母;76mm的机器,一行打印22个汉字,33个字母,80mm的机器,一行打印24个汉字,48个字母
     *                               标签机宽度50mm，一行32个字母，宽度40mm，一行26个字母
     * @return [string]              [返回处理结果字符串]
     */
    function LR($str_left,$str_right,$length){
        if( empty($str_left) || empty($str_right) || empty($length) ) return '请输入正确的参数';
        $kw = '';
        $str_left_lenght = strlen(iconv("UTF-8", "GBK//IGNORE", $str_left));
        $str_right_lenght = strlen(iconv("UTF-8", "GBK//IGNORE", $str_right));
        $k = $length - ($str_left_lenght+$str_right_lenght);
        for($q=0;$q<$k;$q++){
            $kw .= ' ';
        }
        return $str_left.$kw.$str_right;
    }

    $content = '--------------------------------<BR>';
    $content .= LR('总价','88元',32);//左边内容，右边内容，一行占用总长度
    $content .= '--------------------------------<BR>';
    wp_print(SN,$content,1);//调用云打印接口

    /**************************************************************************************************


