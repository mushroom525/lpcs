<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/4/7
 * Time: 16:33
 */

namespace Home\Controller;
use Think\Controller;

class SuggestionController extends Controller
{
    public function add(){
        $callback=$_REQUEST['callback'];
        $data['user_openid']=$_REQUEST['openid'];
        $data['content']=$_REQUEST['content'];
        $suggestion=D('suggestion');
        $sugadd=$suggestion->add($data);
        if($sugadd){
            $arr = array(
                "code" => "000",
                "data" => "",
                "msg" => "发表成功"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $arr = array(
                "code" => "111",
                "data" => "",
                "msg" => "发表失败"
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
}