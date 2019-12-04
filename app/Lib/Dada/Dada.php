<?php

namespace  App\Lib\Dada; // @todo: 这里是要生成类的命名空间


class Dada
{
    const ORDER_ADD_URL = "/api/order/addOrder";
    const SHOP_ADD_URL = "/api/shop/add";
    const CITY_ORDER_URL = "/api/cityCode/list";
    const MERCHANT_ADD_URL = "/merchantApi/merchant/add";


    /**
     *请求失败状态
     */
    const FAIL = "fail";
    const SUCCESS = "success";
    const FAIL_MSG = "接口请求超时或失败";
    const FAIL_CODE = -2;

    /**
     * 达达开发者app_key
     */
    public $app_key = '';

    /**
     * 达达开发者app_secret
     */
    public $app_secret = '';

    /**
     * api版本
     */
    public $v = "1.0";

    /**
     * 数据格式
     */
    public $format = "json";

    /**
     * 商户ID
     */
    public $source_id;

    /**
     * 是否是沙盒
     */
    public $sandbox;

    /**
     * host
     */
    public $host;
    public $url;


    public function __construct($config)
    {
        if (!$config) return;

        $this->sandbox = $config['sandbox'];

        if ($config['sandbox']) {
            $this->source_id = '73753';
            $this->host = "http://newopen.qa.imdada.cn";
        } else {
            $this->source_id = $config['source_id'];
            $this->host = "https://newopen.imdada.cn";
        }

        $this->app_key = $config['app_key'];
        $this->app_secret = $config['app_secret'];
    }


    public function store($bodyConfig)
    {
        $this->url = Dada::SHOP_ADD_URL;
        $this->bodyConfig = $bodyConfig;
    }


    public function merchant($bodyConfig)
    {
        $this->url = Dada::MERCHANT_ADD_URL;
        $this->bodyConfig = $bodyConfig;
    }

    public function order($bodyConfig)
    {
        $this->url = Dada::ORDER_ADD_URL;
        $this->bodyConfig = $bodyConfig;
    }


    public function cityCode($bodyConfig)
    {
        $this->url = "/api/cityCode/list";
        $this->bodyConfig = $bodyConfig;
    }

    public function http($url, $data)
    {
        $this->url = $url;
        if ($this->sandbox) {
            if (isset($data['shop_no'])) {
                $data['shop_no'] = '11047059';
            }
        }

        $this->bodyConfig = $data;
        return  $this->bodyConfig;
    }

    public function request()
    {
        $reqParams = $this->bulidRequestParams();
        $resp = $this->getHttpRequestWithPost(json_encode($reqParams));
        return $this->parseResponseData($resp);
    }

    /**
     * 构造请求数据
     * data:业务参数，json字符串
     */
    public function bulidRequestParams()
    {
        $config = $this->bodyConfig;
        $requestParams = [];
        $requestParams['app_key'] = $this->app_key;
        $requestParams['body'] = json_encode($this->bodyConfig);
        $requestParams['format'] = $this->format;
        $requestParams['v'] = $this->v;
        $requestParams['source_id'] = $this->source_id;
        $requestParams['timestamp'] = time();
        $requestParams['signature'] = $this->_sign($requestParams);
        return $requestParams;
    }
    /**
     * 签名生成signature
     */
    public function _sign($data)
    {
        //1.升序排序
        ksort($data);

        //2.字符串拼接
        $args = "";
        foreach ($data as $key => $value) {
            $args .= $key . $value;
        }
        $args = $this->app_secret . $args . $this->app_secret;
        //3.MD5签名,转为大写
        $sign = strtoupper(md5($args));

        return $sign;
    }
    /**
     * 发送请求,POST
     * @param $url 指定URL完整路径地址
     * @param $data 请求的数据
     */
    public function getHttpRequestWithPost($data)
    {

        $url = $this->host . $this->url;

        // json
        $headers = array(
            'Content-Type: application/json',
        );
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);
        // dump(curl_error($curl)); //如果在执行curl的过程中出现异常，可以打开此开关查看异常内容。
        $info = curl_getinfo($curl);
        curl_close($curl);
        if (isset($info['http_code']) && $info['http_code'] == 200) {
            return $resp;
        }
        return '';
    }
    /**
     * 解析响应数据
     * @param $arr返回的数据
     * 响应数据格式：{"status":"success","result":{},"code":0,"msg":"成功"}
     */
    public function parseResponseData($arr)
    {
        $resp = [];
        if (empty($arr)) {
            $resp['status'] = Dada::FAIL;
            $resp['msg'] = Dada::FAIL_MSG;
            $resp['code'] = Dada::FAIL_CODE;
        } else {
            $data = json_decode($arr, true);
            $resp['status'] = $data['status'];
            $resp['msg'] = $data['msg'];
            $resp['code'] = $data['code'];
            if (!empty($data['result'])) {
                $resp['result'] = $data['result'];
            }
        }
        return $resp;
    }
}
