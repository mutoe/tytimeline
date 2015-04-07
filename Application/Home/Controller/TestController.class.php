<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {

	public function index($share_id = 0) {
		$this -> error('未知原因，如有需要请求助管理员:','',200);
	}
}
