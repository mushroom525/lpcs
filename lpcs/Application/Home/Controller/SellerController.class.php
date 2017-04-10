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
}