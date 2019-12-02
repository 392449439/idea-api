<?php

namespace  App\Http\Controllers; // @todo: 这里是要生成类的命名空间

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\User;
use App\Lib\Dada\Dada;
use App\Listeners\Random;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Illuminate\Support\Carbon;

class TestController extends Controller
{  // @todo AuthController 这里是要生成的类名字

    public function test(Request $request)
    {
        echo `<style>
        .item {
            font-size: 14px;
            line-height: 1.5;
        }
    </style>`;
        $random = new Random();
        echo ('<h1>16位</h1>');
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $appid = $random->getRandom(16);
            echo ("<div class='item'>($i). 16位appid: <b>$appid</b></div>");
        }

        $random = new Random();
        echo ('<h1>32位</h1>');
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $appid = $random->getRandom(32);
            echo ("<div class='item'>($i). 16位appid: <b>$appid</b></div>");
        }



        $pwd = md5($_ENV['APP_KEY'] . '123');

        echo ('<h1>PWD</h1>');
        echo ("<div class='item'>$pwd</b></div>");
    }

    public function out(Request $request)
    {
        $dada = new  Dada([
            "app_key" => 'dadaa3ce8c08a223835',
            "app_secret" => 'd62c2d9a787ecbad5e6bfc27986c3ede',
            "sandbox" => true,
            "source_id" => '73753',
        ]);
        $store_id = 'STORE_' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
        $order_id = 'ORDER_' . Carbon::now()->format('YmdHis') . rand(10000, 99999);

        $dada->http('/api/balance/query', [
            "category" => 1,
        ]);

        $dada->order([
            "shop_no" => "11047059",         //	门店编号，门店创建后可在门店列表和单页查看
            "origin_id" => $order_id,       //	第三方订单ID
            "city_code" => "021",       //	订单所在城市的code（查看各城市对应的code值）
            "cargo_price" => 99.99,         //	订单金额
            "is_prepay" => 0,       //	是否需要垫付 1:是 0:否 (垫付订单金额，非运费)
            "receiver_name" => "李传浩",       //	收货人姓名
            "receiver_address" => "测试地址",        //	收货人地址
            "receiver_phone" => "17621643903",        //	收货人地址
            "receiver_lng" => 121.235258,        //	收货人地址经度（高德坐标系）
            "receiver_lat" => 31.006555,        //	收货人地址维度（高德坐标系）
            "callback" => "s",        //	回调URL（查看回调说明）
        ]);
        $res = $dada->request();
        echo json_encode($res);

        // $dada->merchant([
        //     "mobile" => '17621643903',                                          //	联系人电话
        //     "city_name" => '上海',                                              //	城市名称(如,上海)
        //     "enterprise_name" => '上海益火科技有限公司',                        //	区域名称(如,浦东新区)
        //     "enterprise_address" => '中山中路71号平高世贸中心商城1711',         //	区域名称(如,浦东新区)
        //     "contact_name" => '李传浩',                                             //	区域名称(如,浦东新区)
        //     "contact_phone" => '17621643903',                                             //	区域名称(如,浦东新区)
        //     "email" => '1173197065@qq.com',                                             //	区域名称(如,浦东新区)

        // ]);
        // $dada->store([
        //     [
        //         "station_name" => '怪兽炒饭（上海松江2店）',          //	门店名称
        //         "business" => 1,                                      //	业务类型(食品小吃-1,饮料-2,鲜花-3,文印票务-8,便利店-9,水果生鲜-13,同城电商-19, 医药-20,蛋糕-21,酒品-24,小商品市场-25,服装-26,汽修零配-27,数码-28,小龙虾-29, 其他-5)
        //         "city_name" => '上海',                                //	城市名称(如,上海)
        //         "area_name" => '松江区',                              //	区域名称(如,浦东新区)
        //         "station_address" => '测试地址',                      //	门店地址
        //         "lng" => 121.235258,                                  //	门店经度
        //         "lat" => 31.006555,                                   //	门店纬度
        //         "contact_name" => '李传浩',                           //	联系人姓名
        //         "phone" => '17621643903',                             //	联系人电话
        //         "origin_shop_id" => $store_id,                        //	门店编码,可自定义,但必须唯一;若不填写,则系统自动生成
        //         // "username" => '',                                  //	达达商家app账号(若不需要登陆app,则不用设置)
        //         // "password" => '',                                  //	达达商家app密码(若不需要登陆app,则不用设置)
        //     ],
        // ]);


        return;
        //*********************1.配置项*************************
        $config = new Config(0, false);
        //*********************2.实例化一个model*************************
        $shopModel = new ShopAddModel();
        $shopModel->setOriginShopId($store_id);            // 第三方门店编号，发单时候使用

        // 批量接口
        $shopList = [$shopModel];
        //*********************3.实例化一个api*************************
        $shopAddApi = new AddShopApi(json_encode($shopList));

        //***********************4.实例化客户端请求************************
        $dada_client = new DadaRequestClient($config, $shopAddApi);
        $resp = $dada_client->makeRequest();
        echo json_encode($resp);
    }
    public function outStoreList(Request $request)
    {
        return;

        //*********************1.配置项*************************
        $config = new Config(0, false);
        //*********************2.实例化一个model*************************
        $shopModel = new ShopAddModel();
        $shopModel->setStationName('怪兽炒饭（上海松江2店）');        // 门店名称
        $shopModel->setBusiness(1);
        $shopModel->setCityName('上海');                // 根据实际情况填写
        $shopModel->setAreaName('松江区');
        $shopModel->setStationAddress("测试地址");
        $shopModel->setLat(31.006555); //纬度
        $shopModel->setLng(121.235258); //经度
        $shopModel->setContactName('李传浩');
        $shopModel->setPhone('17621643903');
        $store_id = 'STORE_' . Carbon::now()->format('YmdHis') . rand(10000, 99999);
        $shopModel->setOriginShopId($store_id);            // 第三方门店编号，发单时候使用

        // 批量接口
        $shopList = [$shopModel];
        //*********************3.实例化一个api*************************
        $shopAddApi = new AddShopApi(json_encode($shopList));

        //***********************4.实例化客户端请求************************
        $dada_client = new DadaRequestClient($config, $shopAddApi);
        $resp = $dada_client->makeRequest();
        echo json_encode($resp);
    }
}
