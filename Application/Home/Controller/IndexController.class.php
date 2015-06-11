<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
	public function index() {
		// 初始化首页轮播图
		$banner = M('banner');
		$ban = $banner -> where('status=1') -> order('sort desc,create_time desc') -> select();
		$this -> assign('banner', $ban);

		//获取分类列表
		$catalog = M('catalog');
		$catalog_list = $catalog -> where('status=1') -> order('sort desc') -> getField('catalog_id',true);//获取分类的ID数组
		$this -> assign('catalog', $catalog_list);

		//获取热门分享
		$share = M('share');
		$share_list = array();

		foreach ($catalog_list as $key => $catalog_id) {
			$share_list[$key] = $share -> where('catalog_id=%d AND status>0', $catalog_id) -> order('heat desc') -> limit(10) -> select();
		}
		$this -> assign('share', $share_list);
		$this -> assign('empty', '<div class="empty">没有数据</div>');

		//获取热门标签
		$tag = M('tag');
		$hot_tag = $tag -> limit(4) -> order('status desc,total_share desc') -> where('total_share>=3 and tag_name!="" or status>=100') -> select();
		foreach ($hot_tag as $key => $value) {
			$map['tag_id'] = array('LIKE', '%"'.$value['tag_id'].'"%');
			$hot_tag[$key]['hot_share'] = $share -> where($map) -> order('heat desc') -> limit(3) -> field('share_id,savepath,savename,width,height,tag_id') -> select();
		}
		$this -> assign('active_tag', $hot_tag);

		//获取狂热用户
		$user_info = M('user_info');
		$hot_user = $user_info -> limit(4) -> order('total_share desc') -> where('total_share>=6') -> select();
		foreach ($hot_user as $key => $value) {
			$hot_user[$key]['new_share'] = $share -> where('user_id=%d', $value['user_id']) -> order('create_time desc') -> limit(6) -> field('share_id,savepath,savename,width,height,tag_id') -> select();
		}
		$this -> assign('active_user', $hot_user);

		$this -> display();
	}


}
