<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){

		//如果没有登录 直接跳转到登陆页面
		if(empty($_SESSION['login'])) $this->redirect('Login/login');

		//echo '通过了权限验证<br>';
	}
}