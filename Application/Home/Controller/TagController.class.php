<?php
namespace Home\Controller;
use Think\Controller;
class TagController extends BaseController {
	public function index() {
		$tag = M('tag');
		$tag_list = $tag -> order('total_share desc,create_time desc') -> limit(0,16) -> select();
		$this -> assign('tag', $tag_list);

		$share = M('share');
		foreach ($tag_list as $key => $tag) {
			$map['tag_id'] = array('like', '%'.$tag['tag_id'].'%');
			$share_list[$key] = $share -> where($map) -> order('create_time desc') -> limit(0,8) -> select();
		}
		$this -> assign('share', $share_list);
		//$this -> assign('test', $share_list);


		$this -> display();
	}

}
