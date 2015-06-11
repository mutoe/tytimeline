<?php
namespace Home\Controller;
use Think\Controller;
class SearchController extends BaseController {

	public function index() {
		if(I('get.content')) $this -> redirect('/search/'.I('get.content') );

		$this -> display();
	}

	public function search($content) {
		$content = mb_convert_encoding($content, "UTF-8", "gb2312");
		if(!isset($content)) $this -> redirect('index');

		$count = 0;

		// 查找用户
		$user = M('user');
		$map1['nickname'] = array('LIKE', '%'.$content.'%');
		$user1 = $user -> where($map1) -> getField('user_id',true);

		$user_info = M('user_info');
		$map2['detail'] = array('LIKE', '%'.$content.'%');
		$user2 = $user_info -> where($map2) -> getField('user_id',true);

		$list = array_merge((array)$user1, (array)$user2);
		if($list != null) {
			$map3['user_id'] = array('IN', $list);
			$data = $user_info -> where($map3) -> select();
			$this -> assign('user', $data);
			$count += count($data);
		}

		// 查找标签
		$tag = M('tag');
		$map5['tag_name'] = array('LIKE', '%'.$content.'%');
		$data = $tag -> where($map5) -> select();
		$count += count($data);
		$this -> assign('tag', $data);

		// 查找分享
		$share = M('share');
		$map4['detail'] = array('LIKE', '%'.$content.'%');
		$data = $share -> where($map4) -> select();
		$count += count($data);
		$this -> assign('share', $data);

		$this -> assign('count', $count);
		$this -> assign('content', $content);

		$this -> display();
	}

}
