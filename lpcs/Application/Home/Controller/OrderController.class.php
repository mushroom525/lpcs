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
        $map['order_time']=date('Y-m-d H:i:s', time());
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
        $map['total_amount']=$map['goods_amount']+$map['distribution_cost'];
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
            $Out_trade_no=$map['order_id'];
            $Body='良品菜市订单支付';
            //$Total_fee=$map['total_amount']*100;
            $Total_fee=1;
            $input = new \WxPayUnifiedOrder();
            $input->SetBody($Body);
            $input->SetOut_trade_no($Out_trade_no);
            $input->SetTotal_fee($Total_fee);
            $input->SetNotify_url("http://www.heeyhome.com/lpcs/ThinkPHP/Library/Vendor/Pay/example/notify.php");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => array(
                                  "jsApiParameters"=>$jsApiParameters,
                    "order_id"=>$map['order_id'])
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
    public function orderlist(){
        $callback=$_REQUEST['callback'];
        $user_openid=$_REQUEST['openid'];
        $order=D('order');
        $suggest=D('suggestion');
        $orderlist= D()->table(array('lp_order' => 'o', 'lp_address' => 'a'))->field('a.name,a.phone,o.order_step,o.order_id,o.order_time,o.goods_num,o.total_amount')->where("a.address_id=o.address_id and o.user_openid='%s'",$user_openid)->select();
        foreach($orderlist as $key=>$val){
            switch ($val['order_step']){
                case 0: $order_step_ch='待支付';break;
                case 1: $order_step_ch='待商户接单';break;
                case 2: $order_step_ch='待配送';break;
                case 3: $order_step_ch='已完成';break;
                case 4: $order_step_ch='已取消';break;
            }
            $orderlist[$key]['order_step_ch']=$order_step_ch;
            $map['user_openid']=$user_openid;
            $map['order_id']=$val['order_id'];
            $issuggest=$suggest->where($map)->find();
            if($issuggest){
                $is=1;
            }else{
                $is=0;
            }
            $orderlist[$key]['is_suggest']=$is;
        }
        if($orderlist){
            $arr = array(
                "code" => "000",
                "msg" => "查询成功",
                "data" => $orderlist
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "msg" => "查询失败",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function orderinfo(){
        $callback=$_REQUEST['callback'];
        $order_id=$_REQUEST['order_id'];
        $order=D('order');
        $ordereach=D('order_each');
        $good=D('goods');
        $orderinfo= D()->table(array('lp_order' => 'o', 'lp_address' => 'a'))->field('a.name,a.phone,a.address_id,a.area,a.address,a.room,o.order_step,o.order_id,o.order_time,o.goods_num,o.goods_amount,o.total_amount,o.distribution_cost,o.remark,o.pay_time,o.receiving_time,o.finish_time')->where("a.address_id=o.address_id and o.order_id='%s'",$order_id)->find();
        if($orderinfo){
                switch ($orderinfo['order_step']){
                    case 0: $order_step_ch='待支付';break;
                    case 1: $order_step_ch='待商户接单';break;
                    case 2: $order_step_ch='待配送';break;
                    case 3: $order_step_ch='已完成';break;
                    case 4: $order_step_ch='已取消';break;
                }
                $orderinfo['order_step_ch']=$order_step_ch;
                $ordergoods=$ordereach->where("order_id='%s'",$order_id)->select();
                foreach($ordergoods as $key=>$val){
                    $goodsinfo=$good->where('goods_id='.$val['goods_id'])->find();
                    $goodsinfo['goods_num']=$val['goods_num'];
                    $ordergoods[$key]=$goodsinfo;
                }
                $orderinfo['goods']=$ordergoods;
            $arr = array(
                "code" => "000",
                "msg" => "查询成功",
                "data" => $orderinfo
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "msg" => "查询失败",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function ordercancel(){
        $callback=$_REQUEST['callback'];
        $order_id=$_REQUEST['order_id'];
        $order=D('order');
        $refund=D('refund');
        $order_step=$order->where("order_id='%s'",$order_id)->getField('order_step');
        if($order_step==1 || $order_step==0){
            if($order_step==1){
                $orderinfo=$order->where("order_id='%s'",$order_id)->find();
                $map['wx_orderid']=$orderinfo['wx_orderid'];
                $map['order_id']=$order_id;
                $map['order_amount']=$orderinfo['total_amount'];
                $refundadd=$refund->add($map);
                if($refundadd){
                    $orderedit=$order-> where("order_id='%s'",$order_id)->setField('order_step','4');
                    if($orderedit){
                        $arr = array(
                            "code" => "000",
                            "msg" => "取消成功",
                            "data" => ""
                        );
                        echo $callback . "(" . HHJson($arr) . ")";
                    }else{
                        $arr = array(
                            "code" => "111",
                            "msg" => "取消失败",
                            "data" => ""
                        );
                        echo $callback . "(" . HHJson($arr) . ")";
                    }
                }else{
                    $arr = array(
                        "code" => "111",
                        "msg" => "取消失败",
                        "data" => ""
                    );
                    echo $callback . "(" . HHJson($arr) . ")";
                }
            }
            if($order_step==0){
                $orderedit=$order-> where("order_id='%s'",$order_id)->setField('order_step','4');
                if($orderedit){
                    $arr = array(
                        "code" => "000",
                        "msg" => "取消成功",
                        "data" => ""
                    );
                    echo $callback . "(" . HHJson($arr) . ")";
                }else{
                    $arr = array(
                        "code" => "111",
                        "msg" => "取消失败",
                        "data" => ""
                    );
                    echo $callback . "(" . HHJson($arr) . ")";
                }
            }
        }else{
            $arr = array(
                "code" => "112",
                "msg" => "该订单不能取消",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function orderagain(){
        $callback=$_REQUEST['callback'];
        $order_id=$_REQUEST['order_id'];
        $order=D('order');
        $ordereach=D('order_each');
        $cart=D('cart');
        $goods=D('goods');
        $user_openid=$order->where("order_id='%s'",$order_id)->getField('user_openid');
        $iscart=$cart->where("user_openid='%s'",$user_openid)->select();
        if($iscart){
            $cart->where("user_openid='%s'",$user_openid)->delete();
        }
        $goodslist=$ordereach->where("order_id='%s'",$order_id)->select();
        $is=true;
        foreach($goodslist as $key=>$val){
            $data['goods_id']=$val['goods_id'];
            $data['goods_num']=$val['goods_num'];
            $data['user_openid']=$user_openid;
            $is_show=$goods->where('goods_id='.$data['goods_id'])->getField('if_show');
            if($is_show==1){
                $cartadd=$cart->add($data);
                if(!$cartadd){
                    $is=false;
                }
            }
        }
        if($is){
            $arr = array(
                "code" => "000",
                "msg" => "成功",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "msg" => "失败",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function order_step(){
        $callback=$_REQUEST['callback'];
        $order_id=$_REQUEST['order_id'];
        $order=D('order');
        $order_step=$order->where("order_id='%s'",$order_id)->getField('order_step');
        if($order_step==1){
            $arr = array(
                "code" => "000",
                "msg" => "成功",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "msg" => "失败",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function order_continue(){
        $callback=$_REQUEST['callback'];
        $order_id=$_REQUEST['order_id'];
        $order=D('order');
        $orderinfo=$order->where("order_id='%s'",$order_id)->find();
        if($orderinfo){
            if($orderinfo['order_step']==0){
                //商户基本信息,可以写死在WxPay.Config.php里面，其他详细参考WxPayConfig.php
                define('APPID','wxbb97c4417d90216b');
                define('MCHID', '1451674702');
                define('KEY', 'A2ab61ad191f2f942b391ea397b86c79');
                define('APPSECRET', '549a3b5ba125896f940ad0e236a9b52d');
                vendor('Pay.JSAPI');
                $tools = new \JsApiPay();
                $openId = $orderinfo['user_openid'];
                $Out_trade_no=$order_id;
                $Body='良品菜市订单支付';
                //$Total_fee=$orderinfo['total_amount']*100;
                $Total_fee=1;
                $input = new \WxPayUnifiedOrder();
                $input->SetBody($Body);
                $input->SetOut_trade_no($Out_trade_no);
                $input->SetTotal_fee($Total_fee);
                $input->SetNotify_url("http://www.heeyhome.com/lpcs/ThinkPHP/Library/Vendor/Pay/example/notify.php");
                $input->SetTrade_type("JSAPI");
                $input->SetOpenid($openId);
                $order = \WxPayApi::unifiedOrder($input);
                $jsApiParameters = $tools->GetJsApiParameters($order);
                $arr = array(
                    "code" => "000",
                    "msg" => "",
                    "data" => array(
                        "jsApiParameters"=>$jsApiParameters,
                        "order_id"=>$order_id)
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }else{
                $arr = array(
                    "code" => "112",
                    "msg" => "订单已支付",
                    "data" => ""
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }
    }else{
            $arr = array(
                "code" => "111",
                "msg" => "订单不存在",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
}