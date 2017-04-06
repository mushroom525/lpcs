<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {
	public function login(){
        $this->display();
	}
	public function logout(){
		unset($_SESSION['login']);
		$this->redirect('Login/login');
	}
	public function ajaxlogin(){
        $admin = D('admin');
        $map['name'] = $_POST['name'];
        $map['pwd'] = $_POST['pwd'];
        $info = $admin->where($map)->find();
        if($info){
            $_SESSION['login'] = $info;
            $arr ['status']=1;
            $arr['msg']="登录成功";
        }else{
            $arr ['status']=0;
            $arr['msg']="登录失败";
        }
        $this->ajaxReturn($arr);
    }
}