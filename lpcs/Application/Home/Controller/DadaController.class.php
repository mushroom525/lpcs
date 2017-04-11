<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/4/11
 * Time: 11:05
 */

namespace Home\Controller;

use Think\Controller;

class DadaController extends Controller
{
    public function queryDeliverFee()
    {
        $callback = $_REQUEST['callback'];
        $address_id = $_REQUEST['address_id'];
        $cart = D('cart');
        $address=D('address');
        $amount = 0;
        $data['user_openid'] = $_REQUEST['openid'];
        $result = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'", $data['user_openid'])->select();
        if ($result) {
            $quantity = $cart->where("user_openid='%s'", $data['user_openid'])->sum('goods_num');
            foreach ($result as $key => $val) {
                $amount += ($val['discount_price'] * $val['goods_num']);
            }
        }
        $addressinfo=$address->where('address_id='.$address_id)->find();
        //配置项
        $config = array();
        $config['app_key'] = 'dada8d59266bb284231';
        $config['app_secret'] = '884b2bdcde1b8cd25fd7d98154ca388c';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/queryDeliverFee';
        $obj = new DadaOpenapi($config);
        //请求数据
        $data = array(
            'shop_no' => '11047059',
            'origin_id' => '2017lpcs',
            'city_code' => '0512',
            'cargo_price' => doubleval($amount),
            'is_prepay' => 0,
            'expected_fetch_time' => time() + 900,
            'receiver_name' => $addressinfo['name'],
            'receiver_address' => $addressinfo['address'],
            'receiver_phone' => $addressinfo['phone'],
            'receiver_lat' => 31.63,
            'receiver_lng' => 121.41,
            'callback' => 'http://newopen.imdada.cn/inner/api/order/status/notify'
        );

        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {
                $arr = array(
                    "code" => "000",
                    "msg" => "查询成功",
                    "data" => $obj->getResult()
                );
                echo $callback . "(" . HHJson($arr) . ")";
            } else {
                $arr = array(
                    "code" => "111",
                    "msg" => $obj->getMsg(),
                    "data" => ""
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }
            echo sprintf('code:%s，msg:%s', $obj->getCode(), $obj->getMsg());
        } else {
            //请求异常或者失败
            $arr = array(
                "code" => 222,
                "msg" => "请求异常",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }

    public function citylist()
    {
        $callback = $_REQUEST['callback'];
        //配置项
        $config = array();
        $config['app_key'] = 'dada8d59266bb284231';
        $config['app_secret'] = '884b2bdcde1b8cd25fd7d98154ca388c';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/cityCode/list';
        $obj = new DadaOpenapi($config);
        //请求参数
        $data = "";
        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {
                $arr = array(
                    "code" => "000",
                    "msg" => "查询成功",
                    "data" => $obj->getResult()
                );
                echo $callback . "(" . HHJson($arr) . ")";
            } else {
                //返回失败
            }
        } else {
            //请求异常或者失败
            echo 'except';
        }
    }
}