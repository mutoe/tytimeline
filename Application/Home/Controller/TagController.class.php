<?php
namespace Home\Controller;
use Think\Controller;
class TagController extends BaseController {
	public function index() {
		$tag = M('tag');

		$tag_list = $tag -> where('status!=0') -> order('status desc,total_share desc,create_time desc') -> select();
		$this -> assign('tag', $tag_list);

		$share = M('share');
		$share_list = $share -> limit(300) -> order('create_time desc') -> select();
		$this -> assign('share', $share_list);

		$this -> display();
	}

	public function detail( $tag_id = 0) {
		if(!$tag_id) $this -> redirect('Tag/index');
		$tag = M('tag');
		$data = $tag -> where('tag_id=%d',$tag_id) -> find();
		$this -> assign('data', $data);

		$share = M('share');
		$map['tag_id'] = array('like', '%'.$tag_id.'%');
		$list = $share -> where($map) -> order('create_time desc') -> select();
		$this -> assign('list',$list);

		$this -> display();
	}
}
