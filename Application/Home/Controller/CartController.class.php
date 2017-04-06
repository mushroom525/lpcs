<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/4/1
 * Time: 15:01
 */

namespace Home\Controller;

use Think\Controller;

class CartController extends Controller
{
    public function index(){
        $cart = D('cart');
        $amount = 0;
        $callback = $_REQUEST['callback'];
        $data['user_openid'] = $_REQUEST['openid'];
        $result = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'",$data['user_openid'])->select();
        if($result){
            $quantity = $cart->where("user_openid='%s'",$data['user_openid'])->sum('goods_num');
            foreach ($result as $key => $val) {
                $amount += ($val['discount_price'] * $val['goods_num']);
            }
            $arr = array(
                "code" => "000",
                "msg" => "成功",
                "data" => array(
                    'status' => array(
                        'quantity' => $quantity,      //总数量
                        'amount' => $amount     //总金额
                    ),
                    'carts' => $result,    //购物车列表，包含每个购物车的状态
                )
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function add()
    {
        $callback = $_REQUEST['callback'];
        $data['user_openid'] = $_REQUEST['openid'];
        $data['goods_id'] = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
        $amount = 0;
        /* 是否添加过 */
        $cart = D('cart');
        $iscart = $cart->where($data)->find();
        if ($iscart) {
            $cartupdate = $cart->where($data)->setInc('goods_num',1);
        } else {
            $cartadd = $cart->add($data);
        }
        if ($cartupdate || $cartadd) {
            $quantity = $cart->where("user_openid='%s'",$data['user_openid'])->sum('goods_num');
            $result = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'",$data['user_openid'])->select();
            foreach ($result as $key => $val) {
                $amount += ($val['discount_price'] * $val['goods_num']);
            }
            $arr = array(
                "code" => "000",
                "msg" => "成功",
                "data" => array(
                    'status' => array(
                        'quantity' => $quantity,      //总数量
                        'amount' => $amount     //总金额
                    ),
                    'carts' => $result,    //购物车列表，包含每个购物车的状态
                )
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function del(){
        $callback = $_REQUEST['callback'];
        $data['user_openid'] = $_REQUEST['openid'];
        $data['goods_id'] = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
        $amount = 0;
        /* 购物车是否存在改商品 */
        $cart = D('cart');
        $iscart = $cart->where($data)->find();
        if ($iscart) {
            if($iscart['goods_num']==1){
                $cartdel=$cart->where($data)->delete(); // 删除单条
            }else{
                $cartupdate = $cart->where($data)->setDec('goods_num',1);
            }
            if($cartdel||$cartupdate){
                $quantity = $cart->where("user_openid='%s'",$data['user_openid'])->sum('goods_num');
                if(!$quantity){
                    $quantity=0;
                }
                $result = D()->table(array('lp_cart' => 'c', 'lp_goods' => 'g'))->field('c.user_openid,c.goods_id,c.goods_num,g.goods_name,g.price,g.discount_price,g.unit')->where("c.goods_id=g.goods_id and c.user_openid='%s'",$data['user_openid'])->select();
                foreach ($result as $key => $val) {
                    $amount += ($val['discount_price'] * $val['goods_num']);
                }
                $arr = array(
                    "code" => "000",
                    "msg" => "成功",
                    "data" => array(
                        'status' => array(
                            'quantity' => $quantity,      //总数量
                            'amount' => $amount     //总金额
                        ),
                        'carts' => $result,    //购物车列表，包含每个购物车的状态
                    )
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }else{
                $arr = array(
                    "code" => "111",
                    "data" => "",
                    "msg" => "失败"
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }
        } else {
            $arr = array(
                "code" => "112",
                "data" => "",
                "msg" => "购物车没有该商品"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function emptycart(){
        $callback = $_REQUEST['callback'];
        $data['user_openid'] = $_REQUEST['openid'];
        $cart = D('cart');
        $cartdel=$cart->where($data)->delete();
        if($cartdel){
            $arr = array(
                "code" => "000",
                "data" =>"",
                "msg" => "成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
}