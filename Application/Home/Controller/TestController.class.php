<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends Controller {

	public function index() {
		$uc = new \Ucenter\Client\Client();
		$result = $uc -> uc_user_register('mttttt' , '22222' , '332019319@qq.com');
		dump($result) ;
	}
}
