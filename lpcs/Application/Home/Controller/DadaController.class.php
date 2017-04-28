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
    // 订单运费查询
    public function queryDeliverFee()
    {
        $callback = $_REQUEST['callback'];
        $address_id = $_REQUEST['address_id'];
        $map['user_openid'] = $_REQUEST['openid'];
        $cart = D('cart');
        $address = D('address');
        //配置项
        $config = array();
        $config['app_key'] = 'dada22a40333b3da3c3';
        $config['app_secret'] = '950c0e382e69c8adb256c0b8c8aa5f31';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/queryDeliverFee';
        $obj = new DadaOpenapi($config);
        $amount = 0;
        $quantity = 0;
        $carts = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'", $map['user_openid'])->select();
        if ($carts) {
            $quantity = $cart->where("user_openid='%s'", $map['user_openid'])->sum('goods_num');
            foreach ($carts as $key => $val) {
                $amount += ($val['discount_price'] * $val['goods_num']);
            }
        }
        $addressinfo = $address->where('address_id=' . $address_id)->find();
        //请求数据
        $data = array(
            'shop_no' => '11047059',
            'origin_id' => '2017lpcs',
            'city_code' => '0512',
            'cargo_type' => 12,
            'cargo_price' => doubleval($amount),
            'cargo_num' => $quantity,
            'is_prepay' => 0,
            'expected_fetch_time' => time() + 900,
            'receiver_name' => $addressinfo['name'],
            'receiver_address' => $addressinfo['address'],
            'receiver_phone' => $addressinfo['phone'],
            'receiver_lat' => $addressinfo['receiver_lat'],
            'receiver_lng' => $addressinfo['receiver_lng'],
            'callback' => 'http://newopen.qa.imdada.cn/api/order/status/query'
        );
        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {
                $result = $obj->getResult();
                $fee = $result['fee'];
                $arr = array(
                    "code" => "000",
                    "msg" => "成功",
                    "data" => array(
                        'status' => array(
                            'quantity' => $quantity,      //总数量
                            'amount' => $amount,     //总金额
                            'total' => $amount + $fee
                        ),
                        'carts' => $carts,    //购物车列表，包含每个购物车的状态
                        'fee' => $fee
                    )
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

    //城市列表接口
    public function citylist()
    {
        $callback = $_REQUEST['callback'];
        //配置项
        $config = array();
        $config['app_key'] = 'dada22a40333b3da3c3';
        $config['app_secret'] = '950c0e382e69c8adb256c0b8c8aa5f31';
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

    public function addOrder()
    {
        //TODO 达达回调错误
        $callback = $_REQUEST['callback'];
        $order_id = $_REQUEST['order_id'];
        $order = D('order');
        $orderinfo = D()->table(array('lp_order' => 'o', 'lp_address' => 'a'))->field('a.name,a.phone,a.address_id,a.area,a.address,a.room,o.order_step,o.order_id,o.order_time,o.goods_num,o.goods_amount,o.total_amount,o.distribution_cost,o.remark,o.pay_time,o.receiving_time,o.finish_time')->where("a.address_id=o.address_id and o.order_id='%s'", $order_id)->find();
        //配置项
        $config = array();
        $config['app_key'] = 'dada22a40333b3da3c3';
        $config['app_secret'] = '950c0e382e69c8adb256c0b8c8aa5f31';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/addOrder';
        $obj = new DadaOpenapi($config);
        //请求数据
        $data = array(
            'shop_no' => '11047059',
            'origin_id' => $order_id,
            'city_code' => '0512',
            'cargo_type' => 12,
            'cargo_price' => $orderinfo['goods_amount'],
            'cargo_num' => $orderinfo['goods_num'],
            'is_prepay' => 0,
            'expected_fetch_time' => time() + 900,
            'receiver_name' => $orderinfo['name'],
            'receiver_address' => $orderinfo['address'],
            'receiver_phone' => $orderinfo['phone'],
            'receiver_lat' => $orderinfo['receiver_lat'],
            'receiver_lng' => $orderinfo['receiver_lng'],
            'callback' => 'http://newopen.qa.imdada.cn/api/order/status/query'
        );
        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {

            } else {

            }
            echo sprintf('code:%s，msg:%s', $obj->getCode(), $obj->getMsg());
        } else {
            //请求异常或者失败
            echo 'except';
        }
    }

    public function statusquery()
    {
        $callback = $_REQUEST['callback'];
        $order_id = $_REQUEST['order_id'];
        //配置项
        $config = array();
        $config['app_key'] = 'dada22a40333b3da3c3';
        $config['app_secret'] = '950c0e382e69c8adb256c0b8c8aa5f31';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/status/query';
        $obj = new DadaOpenapi($config);
        //请求数据
        $data = array(
            'order_id' => $order_id,
        );
        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {
                return $obj->getResult();
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
            echo 'except';
        }
    }

    public function accept()
    {
        $order_id = $_REQUEST['order_id'];
        //配置项
        $config = array();
        $config['app_key'] = 'dada22a40333b3da3c3';
        $config['app_secret'] = '950c0e382e69c8adb256c0b8c8aa5f31';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/accept';
        $obj = new DadaOpenapi($config);
        //请求数据
        $data = array(
            'order_id' => $order_id,
        );
        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {

            } else {
            }
            echo sprintf('code:%s，msg:%s', $obj->getCode(), $obj->getMsg());
        } else {
            //请求异常或者失败
            echo 'except';
        }
    }

    public function finish()
    {
        $order_id = $_REQUEST['order_id'];
        //配置项
        $config = array();
        $config['app_key'] = 'dada22a40333b3da3c3';
        $config['app_secret'] = '950c0e382e69c8adb256c0b8c8aa5f31';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/finish';
        $obj = new DadaOpenapi($config);
        //请求数据
        $data = array(
            'order_id' => $order_id,
        );
        //请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {

            } else {
            }
            echo sprintf('code:%s，msg:%s', $obj->getCode(), $obj->getMsg());
        } else {
            //请求异常或者失败
            echo 'except';
        }
    }

    /*
     * TODO
     * 达达未接入，临时去配送接口
    */
    public function dadaTemporary()
    {
        $callback = $_REQUEST['callback'];
        $order_id = $_REQUEST['order_id'];
        $order = D('order');
        $deldefault = $order->where('order_id=' . $order_id)->setField('order_step', '5');
        if ($deldefault) {
            $arr = array("code" => "000",
                "data" => "",
                "msg" => "成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array("code" => "111",
                "data" => "",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }

    /*
     * TODO
     * 达达未接入，临时配送完成接口
    */
    public function dadaFinish()
    {
        $callback = $_REQUEST['callback'];
        $order_id = $_REQUEST['order_id'];
        $order = D('order');
        $deldefault = $order->where('order_id=' . $order_id)->setField('order_step', '3');
        if ($deldefault) {
            $arr = array("code" => "000",
                "data" => "",
                "msg" => "成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array("code" => "111",
                "data" => "",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
}