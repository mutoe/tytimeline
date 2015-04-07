<?php
namespace Admin\Controller;
use Think\Controller;
class SourceController extends BaseController {

	public function index() {
		$this -> display();
	}

	public function shard() {
		$share = M('share');
		$data = $share -> select();

		$this -> assign('data', $data);
		$this -> display();
	}

	public function tag() {
		$tag = M('tag');
		$data = $tag -> select();

		$this -> assign('data', $data);
		$this -> display();
	}
	public function delete_tag($tag_id = 0) {
		$tag_id == 0 && $this -> error('参数传递有误');
		$tag = M('tag');
		$result = $tag -> where('tag_id=%d',$tag_id) -> delete();
		if($result === false){ //因为返回值$result是返回删除的数据数 ，所以使用全等判断SQL语句失败
			$this -> error('数据库操作失败 :-( ');
		} else {
			$this -> success('删除成功');
		}
	}

}
