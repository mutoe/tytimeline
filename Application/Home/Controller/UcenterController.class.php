<?php
namespace Home\Controller;
use Think\Controller;

class UcenterController extends Controller {
	
	public function index($uid='38237') {
		$uc = new \Ucenter\Client\Client();
		$ucresult = $uc-> uc_pm_list($uid);
		print_r($ucresult);
	}	

}
