<?php
namespace Home\Controller;
use Think\Controller;
class CatalogController extends BaseController {

	public function detail($id = 9) {
		$catalog = M('catalog');
		$data = $catalog -> where('catalog_id=%d AND status=1', $id) -> find();
		if(!$data) $this -> error('未知分类');
		$this -> assign('catalog', $data);

		$share = M('share');
		$share_list = $share -> where('catalog_id=%d', $id) -> order('create_time desc') -> select();
		$this -> assign('share', $share_list);

		$this -> display('Ground/catalog');
	}

}
