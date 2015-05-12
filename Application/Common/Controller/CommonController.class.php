<?php
namespace Common\Controller;
use Think\Controller;

class CommonController extends Controller {
	protected function _initialize() {
		// 如果还没有读取站点配置就从数据库调取
		if(C('SITE_VER') == null) {
			$config = M('config') -> getField('key,value');
			C($config);
		}
	}

	/**
	 * IE9 友好弹出
	 */
	public function fuckIE() {
		$this -> display('Common:fuckIE');
	}

	/**
	 * 登出操作
	 */
	public function logout() {
		//清空cookie
		cookie('user_id',null);

		//uc 同步登出
		/*	$uc = new \Ucenter\Client\Client();
		echo $uc -> uc_user_synlogout();*/

		session('user_id',null);
		$this -> success();
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

		$content = $this -> fetch('./Application/Common/View/Modal/'. $modal .'.html');
		if($content) $this -> success($content);
		else $this -> error("异步加载模态窗口失败，请确认存在该方法:". $modal);
	}

}
