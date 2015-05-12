<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
	public function index() {
		// 初始化首页轮播图
		$banner = M('banner');
		$ban = $banner -> where('status=1') -> order('sort desc') -> select();
		$this -> assign('banner', $ban);

		//获取分类列表
		$catalog = M('catalog');
		$catalog_list = $catalog -> where('status=1') -> order('sort desc') -> getField('catalog_id',true);//获取分类的ID数组
		$this -> assign('catalog', $catalog_list);

		//获取热门分享
		$share = M('share');
		$share_list = array();

		foreach ($catalog_list as $key => $catalog_id) {
			$share_list[$key] = $share -> where('catalog_id=%d', $catalog_id) -> order('click desc') -> limit(12) -> select();
		}
		$this -> assign('share', $share_list);
		$this -> assign('empty', '<div class="empty">没有数据</div>');

		/*
		//获取最新分享
		$new_share = $share -> limit(0,8) -> order('create_time desc') -> select();
		$this -> assign('new', $hot_share);*/

		//获取热门标签
		$tag = M('tag');
		$hot_tag = $tag -> limit(0,16) -> order('total_share desc') -> select();
		$this -> assign('tag', $hot_tag);

		//获取狂热用户
		$user_info = M('user_info');
		$hot_user = $user_info -> limit(0,8) -> order('total_share desc') -> select();
		$this -> assign('user', $hot_user);

		$this -> display();
	}


}
