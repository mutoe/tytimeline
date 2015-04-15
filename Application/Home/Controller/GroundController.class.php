<?php
namespace Home\Controller;
use Think\Controller;
class GroundController extends Controller {

	public function index() {
		//获取热门分享
		$share = M('share');
		$hot_share = $share -> limit(50) -> order('click desc') -> select();
		$this -> assign('hot', $hot_share);

		/*
		//获取最新分享
		$new_share = $share -> limit(0,8) -> order('create_time desc') -> select();
		$this -> assign('new', $hot_share);*/

		//获取热门标签
		$tag = M('tag');
		$hot_tag = $tag -> limit(0,16) -> order('total_share desc') -> select();
		$this -> assign('tag', $hot_tag);

		//获取狂热用户
		$user_info = M('user_info');
		$hot_user = $user_info -> limit(0,8) -> order('total_share desc') -> select();
		$this -> assign('user', $hot_user);

		$this -> display();
	}


}
