<?php

namespace  App\Lib\Printer;

use App\Lib\Feieyun\HttpClient;


class Printer
{  // @todo AuthController 这里是要生成的类名字

    private $user = "1173197065@qq.com";
    private $ukey = "6KcZwgp6D5VfEvIz";
    private $sn = "";

    public function printData($header, $body, $footer, $sn)
    {
        //名称14 单价6 数量3 金额6-->这里的字节数可按自己需求自由改写，14+6+3+6再加上代码写的3个空格就是32了，58mm打印机一行总占32字节
        $this->sn = $sn;
        $data = '';

        foreach ($header as  $v) {
            $data .= $v . "<BR>";
        }

        $data .= $this->format($body, 14, 6, 3, 6);

        foreach ($footer as  $v) {
            $data .= $v . "<BR>";
        }

        return $this->print($data);
    }

    private function add($snlist)
    {
        $time = time();                //请求时间
        $content = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printerAddlist',

            'printerContent' => $snlist
        );

        $client = new HttpClient('api.feieyun.cn', 80);
        if (!$client->post('/Api/Open/', $content)) {
            return response()->json([
                'code' => -1,
                'msg' => 'error',
                'data' => null,
            ]);
        } else {
            return response()->json([
                'code' => 1,
                'msg' => 'success',
                'data' => json_decode($client->getContent()),
            ]);
        }
    }


    private function print($data)
    {

        $time = time();                //请求时间
        $content = array(
            'user' => $this->user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printMsg',

            'sn' => $this->sn,
            'content' => $data,
            'times' => 1 //打印次数
        );

        $client = new HttpClient('api.feieyun.cn', 80);

        if (!$client->post('/Api/Open/', $content)) {
            return false;
        } else {
            return json_decode($client->getContent());
        }
    }

    //生成签名
    function signature($time)
    {
        return sha1($this->user . $this->ukey . $time); //公共参数，请求公钥
    }
    function format($arr, $A, $B, $C, $D)
    {
        $orderInfo = '';
        foreach ($arr as $k5 => $v5) {
            $name = $v5['title'];
            $price = $v5['price'];
            $num = $v5['num'];
            $prices = $v5['price'] * $v5['num'];
            $kw3 = '';
            $kw1 = '';
            $kw2 = '';
            $kw4 = '';
            $str = $name;
            $blankNum = $A; //名称控制为14个字节
            $lan = mb_strlen($str, 'utf-8');
            $m = 0;
            $j = 1;
            $blankNum++;
            $result = array();
            if (strlen($price) < $B) {
                $k1 = $B - strlen($price);
                for ($q = 0; $q < $k1; $q++) {
                    $kw1 .= ' ';
                }
                $price = $price . $kw1;
            }
            if (strlen($num) < $C) {
                $k2 = $C - strlen($num);
                for ($q = 0; $q < $k2; $q++) {
                    $kw2 .= ' ';
                }
                $num = $num . $kw2;
            }
            if (strlen($prices) < $D) {
                $k3 = $D - strlen($prices);
                for ($q = 0; $q < $k3; $q++) {
                    $kw4 .= ' ';
                }
                $prices = $prices . $kw4;
            }
            for ($i = 0; $i < $lan; $i++) {
                $new = mb_substr($str, $m, $j, 'utf-8');
                $j++;
                if (mb_strwidth($new, 'utf-8') < $blankNum) {
                    if ($m + $j > $lan) {
                        $m = $m + $j;
                        $tail = $new;
                        $lenght = iconv("UTF-8", "GBK//IGNORE", $new);
                        $k = $A - strlen($lenght);
                        for ($q = 0; $q < $k; $q++) {
                            $kw3 .= ' ';
                        }
                        if ($m == $j) {
                            $tail .= $kw3 . ' ' . $price . ' ' . $num . ' ' . $prices;
                        } else {
                            $tail .= $kw3 . '<BR>';
                        }
                        break;
                    } else {
                        $next_new = mb_substr($str, $m, $j, 'utf-8');
                        if (mb_strwidth($next_new, 'utf-8') < $blankNum) continue;
                        else {
                            $m = $i + 1;
                            $result[] = $new;
                            $j = 1;
                        }
                    }
                }
            }
            $head = '';
            foreach ($result as $key => $value) {
                if ($key < 1) {
                    $v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
                    $v_lenght = strlen($v_lenght);
                    if ($v_lenght == 13) $value = $value . " ";
                    $head .= $value . ' ' . $price . ' ' . $num . ' ' . $prices;
                } else {
                    $head .= $value . '<BR>';
                }
            }
            $orderInfo .= $head . $tail;
            @$nums += $prices;
        }

        return $orderInfo;
    }
}
