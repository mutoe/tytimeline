<?php
namespace Admin\Controller;
use Think\Controller;
class SourceController extends BaseController {

	public function index() {
		$this -> display();
	}

	public function shard($sort = 'share_id', $share_id = 0) {
		$share = M('share');
		$sort .= ' desc';
		if($share_id) {
			$data = $share -> where('share_id=%d', $share_id) -> select();
		} else {
			$data = $share -> order($sort) -> select();
		}

		$this -> assign('data', $data);
		$this -> display();
	}

	public function catalog() {
		$catalog = M('catalog');
		$data = $catalog -> order('sort desc') -> select();

		$this -> assign('data', $data);
		$this -> display();
	}

	public function tag($sort = 'tag_id') {
		$tag = M('tag');
		$sort .= ' desc';
		$data = $tag -> order($sort) -> select();

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
