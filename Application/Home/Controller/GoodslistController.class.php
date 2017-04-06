<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/3/30
 * Time: 15:03
 */

namespace Home\Controller;

use Think\Controller;

class GoodslistController extends Controller
{
    public function todayrecommend()
    {
        $callback = $_GET['callback'];
        $user_openid = $_GET['openid'];
        $goods = D('goods');
        $data['if_show'] = 1;
        $data['tag'] = 1;
        $cart = D('cart');
        $goodslist = $goods->where($data)->order('goods_id desc')->select();
        if ($goodslist) {
            foreach ($goodslist as $key=>$val){
                $map['user_openid']=$user_openid;
                $map['goods_id']=$val['goods_id'];
                $goods_num=$cart->where($map)->getField('goods_num');
                if(!$goods_num){
                    $goods_num=0;
                }
                $goodslist[$key]['goods_num'] = $goods_num;
            }
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $goodslist
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "111",
                "msg" => "信息不存在",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }

    public function goodslist()
    {
        $callback = $_GET['callback'];
        $cate_id = $_GET['cate_id'];
        $user_openid = $_GET['openid'];
        $childcate = D('category');
        $goods = D('goods');
        $cart = D('cart');
        $childcatelist = $childcate->where('parent_id=' . $cate_id)->field('cate_id,cate_name')->order('sort_order', asc)->select();
        foreach ($childcatelist as $key => $val) {
            $map['cate_id'] = array('like', "%{$val['cate_id']}%");
            $map['if_show'] = 1;
            $goodslist = $goods->where($map)->order('goods_id desc')->select();
            foreach ($goodslist as $ke => $va) {
                $truecate_id = $val['cate_id'];
                $goodslist[$ke]['cate_id'] = $truecate_id;
                $data['user_openid']=$user_openid;
                $data['goods_id']=$va['goods_id'];
                $goods_num=$cart->where($data)->getField('goods_num');
                if(!$goods_num){
                    $goods_num=0;
                }
                $goodslist[$ke]['goods_num'] = $goods_num;
            }
            $childcatelist[$key]['goods'] = $goodslist;
        }
        $data['cate_id'] = array('like', "%{$cate_id}%");
        $data['tag'] = 2;
        $data['if_show'] = 1;
        $discountgoods = $goods->where($data)->select();
        $discount = array("cate_id" => "0", "cate_name" => "特惠供应", "goods" => $discountgoods);
        array_unshift($childcatelist, $discount);
        $discountlist = $goods->where('tag=2')->order('goods_id desc')->select();
        $arr = array(
            "code" => "000",
            "msg" => "",
            "data" => $childcatelist
        );
        echo $callback . "(" . HHJson($arr) . ")";
    }
}