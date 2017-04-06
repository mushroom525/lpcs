<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/3/31
 * Time: 13:50
 */

namespace Home\Controller;

use Think\Controller;

class OrderController extends Controller
{
    public function orderproduce(){
        $callback = $_REQUEST['callback'];
        /* 订单号 返回当前的毫秒时间戳(16位) */
        $mtime = explode(' ', microtime());
        $mtime[0] = ($mtime[0] + 1) * 1000000;
        $str1 = (string)$mtime[1];
        $str2 = substr((string)$mtime[0], 1);
        $map['order_id'] = $str1 . $str2;
        $map['user_openid'] = $_REQUEST['openid'];
        $map['address_id']=$_REQUEST['address_id'];
        $map['order_time']=time();
        $map['appointment_time']=$_REQUEST['appointment_time'];
        $map['remark']=$_REQUEST['remark'];
        $amount=0;
        $cart=D('cart');
        $order=D('order');
        $ordereach=D('order_each');
        $quantity = $cart->where("user_openid='%s'",$map['user_openid'])->sum('goods_num');
        $result = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'",$map['user_openid'])->select();
        foreach ($result as $key => $val) {
            $amount += ($val['discount_price'] * $val['goods_num']);
        }
        $map['goods_num']=$quantity;
        $map['goods_amount']=$amount;
        $map['distribution_cost']=$_REQUEST['distribution_cost'];
        $orderadd=$order->add($map);
        if($orderadd){
            $carts=$cart->where("user_openid='%s'",$map['user_openid'])->select();
            foreach($carts as $key=>$val){
                $data['order_id']=$map['order_id'];
                $data['goods_id']=$val['goods_id'];
                $data['goods_num']=$val['goods_num'];
                $ordereach->add($data);
            }
            //商户基本信息,可以写死在WxPay.Config.php里面，其他详细参考WxPayConfig.php
            define('APPID','wxbb97c4417d90216b');
            define('MCHID', '1451674702');
            define('KEY', 'A2ab61ad191f2f942b391ea397b86c79');
            define('APPSECRET', '549a3b5ba125896f940ad0e236a9b52d');
            vendor('Pay.JSAPI');
            $tools = new \JsApiPay();
            $openId = $map['user_openid'];
            $Out_trade_no=date('YHis').rand(100,1000);
            $Total_fee='测试';
            $Body='啥也不说';
            $Total_fee=1;
            $input = new \WxPayUnifiedOrder();
            $input->SetBody($Body);
            $input->SetOut_trade_no($Out_trade_no);
            $input->SetTotal_fee($Total_fee);
            $input->SetNotify_url("http://www.heeyhome.com/pay/notify.php");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $jsApiParameters
            );
            echo $callback . "(" . HHJson($arr) . ")";

        }else{
            $arr = array(
                "code" => "111",
                "msg" => "订单生成失败",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }

    }
}