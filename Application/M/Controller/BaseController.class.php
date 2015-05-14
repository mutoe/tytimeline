<?php
namespace M\Controller;
use Common\Controller\CommonController;

class BaseController extends CommonController {
	protected function _initialize() {
		//dump(I('session.'));
	}

	/**
	 * 异步加载模态窗口
	 */
	public function syncHtml($modal = 'login', $info = null, $status = null, $title = null) {
		if(!IS_AJAX) $this -> error("非法访问");
		$data['info'] = $info;
		$data['status'] = $status == 'success';
		$data['title'] = $title;
		$this -> assign($data);

		$content = $this -> fetch('./Application/M/View/Modal/'. $modal .'.html');
		if($content) $this -> success($content);
		else $this -> error("异步加载模态窗口失败，请确认存在该方法:". $modal);
	}
}