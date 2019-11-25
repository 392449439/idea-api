<?php

	  $arr[0] = array('title'=>'酸菜鱼','price'=>'100.4','num'=>'10');
    $arr[1] = array('title'=>'可乐鸡翅+蒜蓉蒸扇贝','price'=>'10.3','num'=>'6');
    $arr[2] = array('title'=>'紫苏焖鹅+梅菜肉饼+椒盐虾+北京烤鸭','price'=>'10.0','num'=>'8');

    /**
     *    飞鹅技术支持
     *    #########################################################################################################
     *
     *    进行订单的多列排版demo，实现商品超出字数的自动换下一行对齐处理，同时保持各列进行对齐
     *
     *    排版原理是统计字符串字节数，补空格换行处理
     *
     *    58mm的机器,一行打印16个汉字,32个字母;80mm的机器,一行打印24个汉字,48个字母
     *
     *    #########################################################################################################
     */
    function test($arr,$A,$B,$C,$D)
    {
      $orderInfo = '<CB>飞鹅云测试</CB><BR>';
      $orderInfo .= '名称           单价  数量 金额<BR>';
      $orderInfo .= '--------------------------------<BR>';
      foreach ($arr as $k5 => $v5) {
        $name = $v5['title'];
        $price = $v5['price'];
        $num = $v5['num'];
        $prices = $v5['price']*$v5['num'];
        $kw3 = '';
        $kw1 = '';
        $kw2 = '';
        $kw4 = '';
        $str = $name;
        $blankNum = $A;//名称控制为14个字节
        $lan = mb_strlen($str,'utf-8');
        $m = 0;
        $j=1;
        $blankNum++;
        $result = array();
        if(strlen($price) < $B){
              $k1 = $B - strlen($price);
              for($q=0;$q<$k1;$q++){
                    $kw1 .= ' ';
              }
              $price = $price.$kw1;
        }
        if(strlen($num) < $C){
              $k2 = $C - strlen($num);
              for($q=0;$q<$k2;$q++){
                    $kw2 .= ' ';
              }
              $num = $num.$kw2;
        }
        if(strlen($prices) < $D){
              $k3 = $D - strlen($prices);
              for($q=0;$q<$k3;$q++){
                    $kw4 .= ' ';
              }
              $prices = $prices.$kw4;
        }
        for ($i=0;$i<$lan;$i++){
          $new = mb_substr($str,$m,$j,'utf-8');
          $j++;
          if(mb_strwidth($new,'utf-8')<$blankNum) {
            if($m+$j>$lan) {
              $m = $m+$j;
              $tail = $new;
              $lenght = iconv("UTF-8", "GBK//IGNORE", $new);
              $k = $A - strlen($lenght);
              for($q=0;$q<$k;$q++){
                $kw3 .= ' ';
              }
              if($m==$j){
                $tail .= $kw3.' '.$price.' '.$num.' '.$prices;
              }else{
                $tail .= $kw3.'<BR>';
              }
              break;
            }else{
              $next_new = mb_substr($str,$m,$j,'utf-8');
              if(mb_strwidth($next_new,'utf-8')<$blankNum) continue;
              else{
                $m = $i+1;
                $result[] = $new;
                $j=1;
              }
            }
          }
        }
        $head = '';
        foreach ($result as $key=>$value) {
          if($key < 1){
            $v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
            $v_lenght = strlen($v_lenght);
            if($v_lenght == 13) $value = $value." ";
            $head .= $value.' '.$price.' '.$num.' '.$prices;
          }else{
            $head .= $value.'<BR>';
          } 
        }
        $orderInfo .= $head.$tail;
        @$nums += $prices;
      }
      $time = date('Y-m-d H:i:s',time());
      $orderInfo .= '--------------------------------<BR>';
      $orderInfo .= '合计：'.number_format($nums, 1).'元<BR>';
      $orderInfo .= '送货地点：广州市南沙区xx路xx号<BR>';
      $orderInfo .= '联系电话：020-39004606<BR>';
      $orderInfo .= '订餐时间：'.$time.'<BR>';
      $orderInfo .= '备注：加辣<BR><BR>';
      $orderInfo .= '<QR>http://www.feieyun.com</QR>';//把解析后的二维码生成的字符串用标签套上即可自动生成二维码
      return $orderInfo;
    }
    $orderInfo = test($arr,14,6,3,6);//名称14 单价6 数量3 金额6-->这里的字节数可按自己需求自由改写，14+6+3+6再加上代码写的3个空格就是32了，58mm打印机一行总占32字节
    //打开注释可测试
    printMsg(SN,$orderInfo,1);