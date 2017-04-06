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
    public function index(){
        $callback=$_GET['callback'];
        $user_id=$_GET['openid'];
        $address=D('address');
        $data['user_openid']=$user_id;
        $data['is_del']=0;
        $addresslist=$address->where($data)->select();
        if ($addresslist) {
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
}