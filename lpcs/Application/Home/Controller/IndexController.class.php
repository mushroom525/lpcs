<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $callback = $_GET['callback'];
        $user=D('user');
        $code = $_GET['code'];
        $AppId = 'wxbb97c4417d90216b';
        $AppSecret = '549a3b5ba125896f940ad0e236a9b52d';
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $AppId . "&secret=" . $AppSecret . "&code=" . $code . "&grant_type=authorization_code";
        $res = $this->getJson($url);
        $access_token = $res["access_token"];
        $openid = $res['openid'];
        $userinfo=$user->where("openid='%s'",$openid)->find();
        if($userinfo){
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $userinfo
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }else{
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
            $info = $this->getJson($url);
            $data['nickname']=$info['nickname'];
            $data['headimg']=$info['headimgurl'];
            $data['openid']=$info['openid'];
            $user->add($data);
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $info
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
    //$url  接口url string
    //$type 请求类型string
    //$res  返回类型string
    //$arr= 请求参数string
    public function getJson($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}