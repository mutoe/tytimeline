<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {

	public function index() {

		 $info = array(
			'站点版本' => C('SITE_NAME_EN')." ".C('SITE_VER'),
			'操作系统' => PHP_OS,
			'运行环境' => $_SERVER["SERVER_SOFTWARE"],
			'ThinkPHP版本' => THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
			'上传附件限制' => ini_get('upload_max_filesize'),
			'服务器时间' => date("Y年n月j日 H:i:s"),
			'北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600 ),
			'服务器域名/IP' => $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
			'剩余空间' => round((disk_free_space(".")/(1024*1024)),2).'M',
		);
		$this->assign('info',$info);

		$this -> display();
	}

	public function config() {
		$config = M('config');
		$conf_list = $config -> select();
		$this -> assign('data', $conf_list);

		$this -> display();
	}

	public function addConfig() {
		if(I('post.key', 0)) {
			$config = M('config');
			$config -> create();
			$result = $config -> add();
			if($result) {
				$this -> success('添加成功', U('Index/config'));
			} else {
				$info = $config -> getError();
				$this -> error('操作失败:'. $info);
			}
		} else {
			$this -> display();
		}
	}

	public function modConfig($config_id = 0) {
		if($config_id == 0) $this -> error('参数有误！');
		if(I('post.key', 0)) {
			$config = M('config');
			$config -> create();
			$result = $config -> save();
			if($result) {
				$this -> success('操作成功', U('Index/config'));
			} else {
				$info = $config -> getError();
				$this -> error('操作失败:'. $info);
			}
		} else {
			$config = M('config') -> find($config_id);
			$this -> assign('data', $config);

			$this -> display();
		}
	}
	public function delConfig($config_id = 0) {
		if($config_id == 0) $this -> error('参数有误！');
		$config = M('config');
		$result = $config -> delete($config_id);
		if($result == 0)
			$this -> error('删除失败');
		else
			$this -> success($config_id);
	}

}
