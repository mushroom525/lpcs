<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/4/10
 * Time: 15:16
 */

namespace Home\Controller;

use Think\Controller;

class SellerController extends Controller
{
    public function order_confirm(){
        $callback=$_REQUEST['callback'];
        $order_id=$_REQUEST['order_id'];
        $order=D('order');
        $order_step=$order->where("order_id='%s'",$order_id)->getField('order_step');
        if($order_step==1){
            $orderedit=$order-> where("order_id='%s'",$order_id)->setField('order_step','2');
            if($orderedit){
                $receiving_time=date('Y-m-d H:i:s', time());
                $order->where("order_id='%s'",$order_id)->setField('receiving_time',$receiving_time);
                $arr = array(
                    "code" => "000",
                    "msg" => "接单成功",
                    "data" => ""
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }else{
                $arr = array(
                    "code" => "111",
                    "msg" => "接单失败",
                    "data" => ""
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }
        } else{
            $arr = array(
            "code" => "112",
            "msg" => "该订单不是在待接单状态",
            "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function orderlist(){
        $callback=$_REQUEST['callback'];
        $order=D('order');
        $orderlist= D()->table(array('lp_order' => 'o', 'lp_address' => 'a'))->field('a.name,a.phone,o.order_step,o.order_id,o.order_time,o.goods_num,o.total_amount')->where("a.address_id=o.address_id and o.seller_del=0 and (order_step=1 or order_step=2 or order_step=3 or order_step=5) ")->order('id desc')->select();
        if($orderlist){
            foreach($orderlist as $key=>$val){
                switch ($val['order_step']){
                    case 1: $order_step_ch='待商户接单';break;
                    case 2: $order_step_ch='待配送';break;
                    case 3: $order_step_ch='已完成';break;
                    case 5: $order_step_ch='配送中';break;
                }
                $orderlist[$key]['order_step_ch']=$order_step_ch;
            }
            $arr = array(
                "code" => "000",
                "msg" => "查询成功",
                "data" => $orderlist
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else{
            $arr = array(
                "code" => "111",
                "msg" => "查询失败",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
}