<?php
namespace Home\Controller;
use Think\Controller;
class ShardController extends BaseController {
	/**
	 * 详情页面
	 */
	public function detail($share_id = 0) {
		if($share_id == 0) $this -> error('参数错误');

		// 刷新分享数据
		$this -> get_heat($share_id);					// 刷新热度
		$this -> refreshShareData($share_id);	// 刷新分享数据

		// 获取分享详情
		$share = M('share');
		$data = $share -> find($share_id);
		if(!$data) $this -> error('抱歉，该条数据不存在，它可能已被删除');
		$share -> where('share_id=%d', $share_id) -> setInc('click', 1, 30);	// 点击量自增
		$this -> assign('data', $data);

		// 获取评论
		$comment = M('comment');
		$comments = $comment -> where('share_id=%d', $share_id) -> limit(8) -> order('create_time desc') -> select();
		$this -> assign('comment', $comments);

		// 获取用户详情
		$user = M('user_info');
		$user_info = $user -> where('user_id=%d', $data['user_id']) -> find();
		$this -> assign('user_info', $user_info);

		// 获取最近9条分享
		$new_share = $share -> where('user_id=%d', $data['user_id']) -> limit(9) -> order('create_time desc') -> select();
		$this -> assign('new_share', $new_share);

		// 获取该分类下的热门分享
		$cata_hot = $share -> where('catalog_id=%d', $data['catalog_id']) -> limit(6) -> order('click desc') -> select();
		$this -> assign('cata_hot', $cata_hot);

		// 随便看看
		$rand = $share -> order('RAND()') -> limit(1) -> find();
		$this -> assign('rand', $rand);

		$this -> display();
	}

	/**
	 * 计算热度
	 */
	public function get_heat($share_id) {
		$share = M('share');
		$data = $share -> find($share_id);

		$timing = ( time() - $data['create_time'] ) / 3600 / 24;	// 差值时间（天）
		$click = $data['click'];																	// 点击量
		$be_like = $data['be_like'];															// 被收藏数
		$total_comment = $data['total_comments'];									// 被评论数
		$new_share_fix = 100 * 7 / ( 7 + $timing );								// 新作修正系数
		$during_fix = 90 / ( 90 + $timing );											// 旧作品修正系数

		$heat = ( $click + $be_like*6 + $total_comment*4  + $new_share_fix ) * $during_fix;
		$heat = 1 - 62 / ( 62 + $heat );
		$heat = sprintf("%.1f", $heat * 100);
		$result = $share -> where('share_id=%d', $share_id) -> setField('heat', $heat);
		return $result ? $heat : false;
	}

	/**
	 * 刷新分享数据
	 */
	public function refreshShareData($share_id) {
		$share = M('share');
		$data = $share -> find($share_id);

		// 刷新评论数
		$comment = M('comment');
		$data['total_comments'] = $comment -> where('share_id=%d', $share_id) -> count();

		// 刷新喜欢数
		$user_info = M('user_info');
		$map['like_share'] = array('LIKE', '%"'.$share_id.'"%');
		$data['be_like'] = $user_info -> where($map) -> count();

		$result = $share -> field('share_id,total_comments,be_like') -> save($data);
		return $result;
	}

	/**
	 * ajax图片上传处理
	 */
	public function upimg() {
		$config = array(
			'maxSize' 	=> 5*1024*1024,
			'exts'			=> array('jpg', 'gif', 'png', 'jpeg'),
			'rootPath'	=> './Public/',
			'savePath'	=> 'uploads/',
			'autoSub' 	=> true,
			'subName' 	=> array('date', 'Ym'),
    );
		$upload = new \Think\Upload($config); // 实例化上传类
		// 上传文件
		$info = $upload -> uploadOne($_FILES['addimg']);
		if (!$info) {	// 上传错误提示错误信息
			$info = $upload -> getError();
			$this -> error($info);
		} else {	// 上传成功

			// 获取图像EXIF时间信息
			$exif = exif_read_data('./Public/'. $info['savepath']. $info['savename'] , 'IFD0');
			if($exif['DateTimeOriginal'] != null) {
				$exif_time = $exif['DateTimeOriginal'];
			} else {
				$exif_time = $exif['DateTime'];
			}
			if($exif_time != null) {
				$exif_time = strtotime( $exif_time );
			} else {
				$exif_time = time();
			}

			// 写入数据库
			$share = M('share');
			$user_id = is_login();
			$data = array(
				'user_id' => $user_id,
				'savepath' => $info['savepath'],
				'savename' => $info['savename'],
				'time' => $exif_time,
				'create_time' => time(),
				'month' => date('Ym', $exif_time),
			);
			$result = $share -> add($data);//return $share_id

			if($result) {
				// 总分享数自增
				$user_info = M('user_info');
				$user_info -> where('user_id=%d',$user_id) -> setInc('total_share');
				// 初始化image类
		    $image = new \Think\Image();
		    $image -> open('./Public/'. $info['savepath']. $info['savename'] );
				// 计算宽高
				$size = $image -> size();
				$data2['width'] = $size[0];
				$data2['height'] = $size[1];
				// 如果是壁纸，自动添加1号标签
				$is_wallpaper = $this -> isWallpaper( $data2['width'] , $data2['height'] );
				if( $is_wallpaper ) $data2['tag_id'] = '["1"]';

				// 写入数据
				$share -> where('share_id=%d', $result) -> save($data2);

		    // 生成缩略图
		    $image -> thumb(360, 2500) -> save('./Public/'.$info['savepath']. 't_'. $info['savename'] );

				// 嵌入水印
		    $image -> open('./Public/'. $info['savepath']. $info['savename'] );
				$image -> text( '            '. C('WATERMARK_TEXT') ,'./Public/fonts/msyhbd.ttf', 14, '#ffffff40', 8 , -50 );
				$image -> text( "@". get_nickname($user_id) ,'./Public/fonts/msyhbd.ttf', 20, '#ffffff40', 8 , -20 );
				$image -> save('./Public/'. $info['savepath']. $info['savename'] );

				// 嵌入水印（缩略图）
		    $image -> open('./Public/'.$info['savepath']. 't_'. $info['savename']);
				$image -> text( '           '. C('WATERMARK_TEXT') ,'./Public/fonts/msyh.ttf', 10, '#ffffff40', 7 , -30 );
				$image -> text( "       @". get_nickname($user_id) ,'./Public/fonts/msyh.ttf', 12, '#ffffff20', 7 , -10 );
				$image -> save('./Public/'.$info['savepath']. 't_'. $info['savename']);

				$this -> success($result);	// 返回share_id
			} else {
				unlink('./Public/'.$info['savepath'].$info['savename']);
				$this -> error('数据写入失败，请联系管理员；',true);
			}
		}
	}
	/**
	 * 判断图片是否为壁纸的方法，用来自动添加'壁纸'标签
	 */
	private function isWallpaper($w = 0, $h = 0) {
		$wlist = array("1024x768", "1152x864", "1280x768", "1280x800", "1280x1024", "1440x900", "1600x900", "1680x1050", "1920x1080",
							"1600x1200", "1366x768", "1920x1440", "1920x1440", "1920x1200");
		$data = $w. "x". $h;
		return in_array($data, $wlist)?true:false;
	}

	/**
	 * 图片信息修改页面
	 */
	public function modi($share_id = 0) {
		$temp = M('share');
		//检查权限
		if(is_admin()) {
      // 推送通知
      $this -> setNotice(12, $data['user_id'], $share_id);
    } else {
      // 检查是否有删除权限
  		if(!get_auth('modify', 0, $share_id)) $this -> error('非法操作！');
    }

		if(!I('param.time', 0)) {	//如果不是提交请求

			$share = M('share');
			$data = $share -> find($share_id);
			if(!$data) $this -> error('抱歉，该条数据可能已被删除');
			$this -> assign('data', $data);

			// 获取当前时间
			$now = date('Y-m-d H:i');
			$this -> assign('now', $now);

			// 获取推荐标签
			$tag = M('tag');
			$tags = $tag -> where('status>1') -> order('status desc') -> getField('tag_id',true);
			$tags = json_encode($tags);
			$this -> assign('hot_tag', $tags);

			$catalog = M('catalog');
			$catalog_list = $catalog -> where('status>0') -> order('sort desc') -> select();
			$this -> assign('catalog', $catalog_list);

			$this -> display();
		} else {
			// 处理提交请求
			$share = D('share');
			if (!$info = $share -> create()){
				$this -> error($share -> getError());	// 自动验证失败
			} else {
				// 验证通过
				$share -> month = date('Ym', strtotime(I('post.time')));
				$result = $share -> save();
				$result = $this -> refreshTotalShare($share_id);
				if($result !== false) {
					$this -> success('操作成功！');
				} else {
					$this -> error('未知原因，如有需要请求助管理员:'.$share -> getError());
				}
			}
		}
	}

	/**
	 * 删除分享 (伪)
   * 此处删除只是标记分享status为0
   * @author mt
	 */
	public function deleteShare($share_id = 0) {
		if($share_id == 0) $this -> error('参数错误');
		$share = M('share');
		$data = $share -> where('share_id=%d',$share_id) -> find();
		if(!$data) $this -> error('该条数据不存在！');
		// 检查权限
		if(is_admin()) {
		  // 推送通知
			$this -> setNotice(13, $data['user_id'], $share_id);
		} else {
			// 检查是否有删除权限
			if(!get_auth('delete', 0, $share_id)) $this -> error('非法操作！');
		}

		// 通过权限检查，标记字段为已删除
		$share -> where('share_id=%d', $share_id) -> setField('status', 0);

		// 刷新各项类目分享数据
		$this -> refreshTotalShare($share_id);

		$this -> success('删除成功，正在跳转到首页...', U('Index/index'));

	}

	/**
	 * 喜欢分享
	 */
	public function like($share_id = 0) {
		if($share_id == 0) $this -> error('参数错误');
		$user_id = is_login();
		if(!$user_id) $this -> error('请先登录');


		$user_info = M('user_info');
		$like_list = $user_info -> where('user_id=%d', $user_id) -> getField('like_share');
		$like_array = json_decode($like_list);

		// 取得喜欢状态
		if( in_array($share_id, $like_array) ) {

			// 如果喜欢过了，那么取消喜欢
			foreach ($like_array as $key=>$value) if($value == $share_id) {
				array_splice($like_array, $key, 1);
				$flag = false;
				break;
			}
		} else {
			// 还没喜欢，写入数据
			array_push($like_array, $share_id);
			$flag = true;
		}
		$like_list = json_encode($like_array);	// 封装数据

		// 保存数据
		$result = $user_info -> where('user_id=%d', $user_id) -> setField('like_share', $like_list);
		if($result) {

			// 数据更新
			$this -> refreshTotalShare($share_id);

      // 推送通知
      $share = M('share');
      $o = $share -> where('share_id=%d', $share_id) -> getField('user_id');
      $this -> setNotice(22, $o, $share_id);

			$this -> success($flag ?"1":"0");
		} else {
			$this -> error("出错了:".$user_info -> getError());
		}
	}

	/**
	 * 提交评论
	 */
	public function submit_comment($share_id = 0) {
		if(!is_login()) $this -> error('请先登录');
		if($share_id == 0) $this -> error('参数有误');

		$comment = M('comment');
		$data['user_id'] = is_login();
		$data['share_id'] = $share_id;
		$data['detail'] = I('post.detail');
		$data['create_time'] = time();
		$result = $comment -> add($data);	// return了一个comment_id

		if($result) {
		  // 数据刷新
			$share = M('share');
			$share -> where('share_id=%d', $share_id) -> setInc('total_comments');

      // 推送通知
      $o = $share -> where('share_id=%d', $share_id) -> getField('user_id');
      $this -> setNotice(21, $o, $share_id);

			$this -> success('成功了！');
		} else {
			$this -> error('数据写入失败');
		}
	}
	/**
	 * 删除评论
	 */
	public function delete_comment($comment_id = 0) {
		if( !get_auth('manage_comment', 0, $comment_id) ) $this -> error('没有权限');
		$comment = M('comment');
		$share_id = $comment -> where('comment_id=%d',$comment_id) -> getField('share_id');
		$result = $comment -> delete($comment_id);
		if($result) {
			$share = M('share');
			$share -> where('share_id=%d', $share_id) -> setDec('total_comments');
			$this -> success('删除成功');
		} else {
			$this -> error('操作失败！');
		}
	}

	/**
	 * 根据share_id刷新总分享数
	 * @return boolen
	 */
	private function refreshTotalShare($share_id = 0) {
		if(!$share_id) return false;
		$share = M('share');

		// 刷新标签下的分享数目
		$data = $share -> where('share_id=%d', $share_id) -> getField('tag_id');
		if(!$data) return false;
		$tags = json_decode($data);
		foreach ($tags as $value) {
			if(!$value) return false;
			$map['tag_id'] = array('like', '%"'. $value .'"%');
			$map['status'] = array('gt', '0');
			$data = $share -> where($map) -> field('share_id') -> select();
			$count = count($data);
			$tag = M('tag');
			$tag -> where('tag_id=%d', $value) -> setField('total_share', $count);
		}

		// 刷新用户信息下的分享数目
		$user = $share -> where('share_id=%d', $share_id) -> getField('user_id');
		if(!$user) return false;
		$data = $share -> where('user_id=%d AND status>0', $user) -> field('share_id') -> select();
		$count = count($data);
		$user_info = M('user_info');
		$user_info -> where('user_id=%d', $user) -> setField('total_share', $count);

		// 刷新分类信息下的分享数目
		$cata = $share -> where('share_id=%d', $share_id) -> getField('catalog_id');
		if(!$cata) return false;
		$data = $share -> where('catalog_id=%d AND status>0', $cata) -> field('share_id') -> select();
		$count = count($data);
		$catalog = M('catalog');
		$catalog -> where('catalog_id=%d AND status>0', $cata) -> setField('total_share', $count);

	}


}


