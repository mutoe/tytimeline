<?php
namespace Admin\Controller;
use Think\Controller;
class ConfigController extends BaseController {

	public function base() {
		$config = M('config') -> getField('key,value');
		$this -> assign('data', $config);

		$this -> display();
	}

	public function submitBase() {
		$config = M('config');
		$data = I('post.');
		foreach ($data as $key => $value) {
			$map['key'] = $key;
			$config -> where($map) -> setField('value', $value);
		}
		$this -> success('操作成功');
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
				$this -> success('添加成功', U('config'));
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
				$this -> success('操作成功', U('config'));
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
		$conf = $config -> find($config_id);
		if($conf['attr'] == 0) $this -> error('该条配置不可删除');
		$result = $config -> delete($config_id);
		if($result == 0)
			$this -> error('删除失败');
		else
			$this -> success($config_id);
	}

}
