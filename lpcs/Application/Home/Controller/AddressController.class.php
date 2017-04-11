<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/3/31
 * Time: 12:35
 */

namespace Home\Controller;
use Think\Controller;

class AddressController extends Controller
{
    public function notover(){
        $callback=$_GET['callback'];
        $user_id=$_GET['openid'];
        $maxdistance=10000;
        $seller_lat=31.298587;
        $seller_lng=120.749575;
        $data['user_openid']=$user_id;
        $data['is_del']=0;
        $addresslist=array();
        $address=D('address');
        $alllist=$address->where($data)->order('address_id desc')->select();
        if($alllist){
            foreach($alllist as $key=>$val){
                $distance=$this->getDistance($val['receiver_lat'],$val['receiver_lng'],$seller_lat,$seller_lng);
                if($distance>$maxdistance){
                    $addresslist=$alllist[$key];
                }
            }
            if($addresslist){
                $arr = array(
                    "code" => "000",
                    "data" => $addresslist,
                    "msg" => "成功"
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }else{
                $arr = array(
                    "code" => "111",
                    "data" => "",
                    "msg" => "信息不存在"
                );
                echo $callback . "(" . HHJson($arr) . ")";
            }
        }else{
            $arr = array(
                "code" => "112",
                "data" => "",
                "msg" => "未添加地址"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function index(){
        $callback=$_GET['callback'];
        $user_id=$_GET['openid'];
        $address=D('address');
        $data['user_openid']=$user_id;
        $data['is_del']=0;
        $maxdistance=10000;
        $seller_lat=31.298587;
        $seller_lng=120.749575;
        $addresslist=$address->where($data)->order('address_id desc')->select();
        if ($addresslist) {
            foreach($addresslist as $key=>$val){
                $distance=$this->getDistance($val['receiver_lat'],$val['receiver_lng'],$seller_lat,$seller_lng);
                if($distance>$maxdistance){
                    $addresslist[$key]['over']=1;
                }else{
                    $addresslist[$key]['over']=0;
                }
            }
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $addresslist
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "信息不存在"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function add(){
        $callback=$_REQUEST['callback'];
        $data['user_openid']=$_REQUEST['openid'];
        $data['name']=$_REQUEST['name'];
        $data['sex']=$_REQUEST['sex'];
        $data['phone']=$_REQUEST['phone'];
        $data['area']=$_REQUEST['area'];
        $data['address']=$_REQUEST['address'];
        $data['room']=$_REQUEST['room'];
        $data['receiver_lat']=$_REQUEST['receiver_lat'];
        $data['receiver_lng']=$_REQUEST['receiver_lng'];
        $address=D('address');
        $addaddress=$address->add($data);
        if ($addaddress) {
            $arr = array(
                "code" => "000",
                "data" => "",
                "msg" => "添加成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "111",
                "data"=>"",
                "msg" => "添加失败",
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function setdefault(){
        $callback = $_REQUEST['callback'];
        $address_id = $_REQUEST['address_id'];
        $map['user_openid'] = $_REQUEST['openid'];
        $map['is_default']=1;
        $address=D('address');
        /*将该用户id下的所有收货地址的is_default字段设置为0*/
        $setaddress=$address-> where($map)->setField('is_default','0');
        /*将传递过来的地址id的is_default字段设置为1*/
        $setdefault=$address-> where('address_id='.$address_id)->setField('is_default','1');
        if ($setdefault) {
            $arr = array("code" => "000",
                "data" => "",
                "msg" => "成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array("code" => "111",
                "data" =>"",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function del(){
        $callback=$_GET['callback'];
        $address_id=$_GET['address_id'];
        $address=D('address');
        $deldefault=$address-> where('address_id='.$address_id)->setField('is_del','1');
        if($deldefault){
            $arr = array("code" => "000",
                "data" => "",
                "msg" => "成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array("code" => "111",
                "data" =>"",
                "msg" => "失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function addressinfo(){
        $callback=$_GET['callback'];
        $address_id=$_GET['address_id'];
        $address=D('address');
        $addressinfo=$address->where('address_id='.$address_id)->find();
        if ($addressinfo) {
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $addressinfo
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "信息不存在"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function edit(){
        $callback=$_REQUEST['callback'];
        $address_id=$_REQUEST['address_id'];
        $data['name']=$_REQUEST['name'];
        $data['sex']=$_REQUEST['sex'];
        $data['phone']=$_REQUEST['phone'];
        $data['area']=$_REQUEST['area'];
        $data['address']=$_REQUEST['address'];
        $data['room']=$_REQUEST['room'];
        $data['receiver_lat']=$_REQUEST['receiver_lat'];
        $data['receiver_lng']=$_REQUEST['receiver_lng'];
        $address=D('address');
        $updateaddress=$address->where('address_id='.$address_id)->save($data);
        if ($updateaddress===false) {
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "更新失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "000",
                "data" => "",
                "msg" => "更新成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $lng1=$this->fd($lng1,-180,180);
        $lat1=$this->jd($lat1,-74,74);
        $lng2=$this->fd($lng2,-180,180);
        $lat2=$this->jd($lat2,-74,74);
        //返回两点距离，单位米
        return $this->ce($this->yk($lng1),$this->yk($lng2),$this->yk($lat1),$this->yk($lat2));
    }
    private function fd($a, $b, $c) {
        for(; $a > $c;)
            $a -= $c - $b;
        for(; $a < $b;)
            $a += $c - $b;
        return $a;
    }
    private function jd($a, $b, $c) {
        $b != null && ($a = max($a, $b));
        $c != null && ($a = min($a, $c));
        return $a;
    }
    private function yk($a) {
        return 3.141592653589793 * $a / 180;
    }
    private function ce($a, $b, $c, $d) {
       $dO = 6370996.81;
       return $dO * acos(sin($c) * sin($d) + cos($c) * cos($d) * cos($b - $a));
    }
}