<?php
namespace Home\Controller;
use Think\Controller;

class TimeController extends BaseController {
	public function index($uid = 0) {
		if(!$uid) $uid = is_login();//没有传参则默认查看自己的时光轴
		if(!$uid) $this -> error('要查看自己的时光轴，请先登录');//未登录

		$share = M('share');

		//获取月份列表用于时间轴导航
		$temp = $share -> where('user_id=%d', $uid) -> order('time desc') -> field('month') -> distinct(true) -> select();

		/**
		 * 处理月份数据
		 */
		$list = array();
		//获取年份表
		foreach ($temp as $key => $value) {
			$y = substr($value['month'], 0, 4);
			if(!in_array($y, $list[0])) $list[$y] = array();
		}
		//获取月份表
		foreach ($temp as $key => $value) {
			$y = substr($value['month'], 0, 4);
			$m = substr($value['month'], 4, 2);
			if(!in_array($m, $list[$y])) $list[$y][] = $m;
		}
		$this -> assign('list',$list);

		//按照月份查询数据
		foreach ($temp as $key => $value) {
			$data[$key] = $share -> where('user_id=%d and month=%d', $uid, $value['month']) -> order('time desc') -> limit(5) -> select();
		}
		$this -> assign('data',$data);

		$this -> display('timeline-v2');
	}
}
