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
        $address=D('address');
        //配置项
        $config = array();
        $config['app_key'] = 'dada8d59266bb284231';
        $config['app_secret'] = '884b2bdcde1b8cd25fd7d98154ca388c';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/queryDeliverFee';
        $obj = new DadaOpenapi($config);
        $amount = 0;
        $carts = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'", $map['user_openid'])->select();
        if ($carts) {
            $quantity = $cart->where("user_openid='%s'", $map['user_openid'])->sum('goods_num');
            foreach ($carts as $key => $val) {
                $amount += ($val['discount_price'] * $val['goods_num']);
            }
        }
        if($address_id){
            $addressinfo=$address->where('address_id='.$address_id)->find();
            //请求数据
            $data = array(
                'shop_no' => '11047059',
                'origin_id' => '2017lpcs',
                'city_code' => '0512',
                'cargo_type'=> 12,
                'cargo_price' => doubleval($amount),
                'cargo_num'=> $quantity,
                'is_prepay' => 0,
                'expected_fetch_time' => time() + 900,
                'receiver_name' => $addressinfo['name'],
                'receiver_address' => $addressinfo['address'],
                'receiver_phone' => $addressinfo['phone'],
                'receiver_lat' => $addressinfo['receiver_lat'],
                'receiver_lng' => $addressinfo['receiver_lng'],
                'callback' => 'http://newopen.imdada.cn/inner/api/order/status/notify'
            );
            //请求接口
            $reqStatus = $obj->makeRequest($data);
            if (!$reqStatus) {
                //接口请求正常，判断接口返回的结果，自定义业务操作
                if ($obj->getCode() == 0) {
                    $result=$obj->getResult();
                    $fee=$result['fee'];
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
        }else{
            $addresslist=$address->where("user_openid='%s'",$map['user_openid'])->order('address_id desc')->select();
            if($addresslist){
                $addressinfo=$addresslist[0];
                //请求数据
                $data = array(
                    'shop_no' => '11047059',
                    'origin_id' => '2017lpcs',
                    'city_code' => '0512',
                    'cargo_type'=> 12,
                    'cargo_price' => doubleval($amount),
                    'cargo_num'=> $quantity,
                    'is_prepay' => 0,
                    'expected_fetch_time' => time() + 900,
                    'receiver_name' => $addressinfo['name'],
                    'receiver_address' => $addressinfo['address'],
                    'receiver_phone' => $addressinfo['phone'],
                    'receiver_lat' => $addressinfo['receiver_lat'],
                    'receiver_lng' => $addressinfo['receiver_lng'],
                    'callback' => 'http://newopen.imdada.cn/inner/api/order/status/notify'
                );
                //请求接口
                $reqStatus = $obj->makeRequest($data);
                if (!$reqStatus) {
                    //接口请求正常，判断接口返回的结果，自定义业务操作
                    if ($obj->getCode() == 0) {
                        $result=$obj->getResult();
                        $fee=$result['fee'];
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
                        "code" => "222",
                        "msg" => "请求异常",
                        "data" => ""
                    );
                    echo $callback . "(" . HHJson($arr) . ")";
                }
            }else{
                 $fee=0;
            }
        }
        $arr = array(
            "code" => "000",
            "msg" => "成功",
            "data" => array(
                'status' => array(
                    'quantity' => $quantity,      //总数量
                    'amount' => $amount,     //总金额
                    'total'=>$amount+$fee
                ),
                'carts' => $carts,    //购物车列表，包含每个购物车的状态
                'fee'=>$fee
            )
        );
        echo $callback . "(" . HHJson($arr) . ")";
    }
    //城市列表接口
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
    public function addOrder(){
        $callback = $_REQUEST['callback'];
        $order_id = $_REQUEST['order_id'];
        $order=D('order');
        $orderinfo=$order->where("order_id='%s'",$order_id)->find();
        //配置项
        $config = array();
        $config['app_key'] = 'dadaf3f03dc32b07ed0';
        $config['app_secret'] = '7e4e615af165fe63cbf40e52abbc79e8';
        $config['source_id'] = '73753';
        $config['url'] = 'http://newopen.qa.imdada.cn/api/order/addOrder';
        $obj = new DadaOpenapi($config);
        //请求数据
        $data = array(
            'shop_no'=> '11047059',
            'origin_id'=> $orderinfo['order_id'],
            'city_code'=> '0512',
            'cargo_type'=> 12,
            'cargo_price'=> $orderinfo['goods_amount'],
            'cargo_num'=> $orderinfo['goods_num'],
            'is_prepay'=> 0,
            'expected_fetch_time'=> 1471536000,
            'expected_finish_time'=> 0,
            'invoice_title'=> '测试',
            'receiver_name'=> '测试',
            'receiver_address'=> '上海市崇明岛',
            'receiver_phone'=> '18588888888',
            'receiver_tel'=> '18599999999',
            'receiver_lat'=> 31.63,
            'receiver_lng'=> 121.41,
            'callback'=>'http://newopen.imdada.cn/inner/api/order/status/notify'
        );

//请求接口
        $reqStatus = $obj->makeRequest($data);
        if (!$reqStatus) {
            //接口请求正常，判断接口返回的结果，自定义业务操作
            if ($obj->getCode() == 0) {
                //返回成功 ....
            }else{
                //返回失败
            }
            echo sprintf('code:%s，msg:%s', $obj->getCode(), $obj->getMsg());
        }else{
            //请求异常或者失败
            echo 'except';
        }
    }
}