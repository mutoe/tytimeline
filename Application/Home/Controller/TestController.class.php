<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends BaseController {

	public function index() {
		$share = M('share');
		$list = $share -> field('share_id,tag_id') -> select();
		foreach ($list as $key => $value) {
			$arr = explode(',', $value['tag_id']);
			$json = json_encode($arr);
			$share -> where('share_id=%d', $value['share_id']) -> setField('tag_id', $json);
		}
	}
}
