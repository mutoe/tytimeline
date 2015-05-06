<?php
namespace Home\Controller;
use Think\Controller;
class ShardController extends BaseController {
	/**
	 * 详情页面
	 */
	public function detail($id = 0) {
		if($id == 0) $this -> error('参数错误');
		$share = M('share');
		// 获取分享详情
		$data = $share -> where('share_id=%d',$id) -> find();
		if(!$data) $this -> error('抱歉，该条数据可能已被删除');
		$share -> where('share_id=%d', $id) -> setInc('click');//点击量自增
		$this -> assign('data', $data);
		// 获取评论
		$comment = M('comment');
		$comments = $comment -> where('share_id=%d', $id) -> limit(8) -> order('create_time desc') -> select();
		$this -> assign('comment', $comments);
		// 获取用户详情
		$user = M('user_info');
		$user_info = $user -> where('user_id=%d', $data['user_id']) -> find();
		$this -> assign('user', $user_info);

		$this -> display();
	}

	/**
	 * ajax图片上传处理
	 */
	public function upimg() {
		$config = array(
			'maxSize' 	=> 3145728,
			'exts'			=> array('jpg', 'gif', 'png', 'jpeg'),
			'rootPath'	=> './Public/',
			'savePath'	=> 'uploads/',
			'autoSub' 	=> true,
			'subName' 	=> array('date', 'Ym'),
    );
		$upload = new \Think\Upload($config); // 实例化上传类
		// 上传文件
		$info = $upload -> uploadOne($_FILES['addimg']);
		if (!$info) {// 上传错误提示错误信息
			$info = $upload -> getError();
			$this -> error($info);
		} else {// 上传成功

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

				$this -> success($result);//返回share_id
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
		if(!get_auth('modify', 0, $share_id)) $this -> error('非法操作！');
		if(!I('param.time', 0)) {
			//如果不是提交请求
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
			$catalog_list = $catalog -> where('status=1') -> order('sort desc') -> select();
			$this -> assign('catalog', $catalog_list);

			$this -> display();
		} else {
			//处理提交请求
			$share = D('share');
			if (!$share -> create()){
				$this -> error($share -> getError());//自动验证失败
			} else {
				// 验证通过
				$share -> month = date('Ym', strtotime(I('post.time')));
				$result = $share -> save();
				if($result !== false) {
					$this -> success('操作成功！');
				} else {
					$this -> error('未知原因，如有需要请求助管理员:'.$share -> getError());
				}
			}
		}
	}

	/**
	 * 删除分享
	 */
	public function del($share_id = 0) {
		if($share_id == 0) $this -> error('参数错误');
		$share = M('share');
		$data = $share -> where('share_id=%d',$share_id) -> find();
		if(!$data) $this -> error('该条数据不存在！');
		//检查权限
		if(!get_auth('delete', 0, $share_id)) $this -> error('非法操作！');//如果不是自己的分享并且没有删除权限

		$address = './Public/'.$data['savepath'].$data['savename'];
		$address_t = './Public/'.$data['savepath'].'t_'.$data['savename'];
		$result = file_exists($address) ? unlink($address) : true;
		$result = file_exists($address_t) ? unlink($address_t) : true;
		if($result) {
			//删除数据
			$this -> del_tagDec($share_id);
			$result = $share -> delete();
			//总分享数自减
			$user_info = M('user_info');
			$user_info -> where('user_id=%d',$data['user_id']) -> setDec('total_share');

			if($result) {
				$this -> success('删除成功，正在跳转到首页...', U('Index/index'));
			} else {
				$this -> error('删除失败，请联系管理员.删除数据失败');
			}
		} else {
			$this -> error('删除失败，请联系管理员.移除资源出错');
		}
	}
	/**
	 * 删除分享时tag的处理
	 */
	private function del_tagDec($share_id = 0) {
		if(!$share_id) return false;
		$share = M('share');
		$tag = M('tag');
		$tag_str = $share -> where('share_id=%d', $share_id) -> getField('tag_id');
		$tag_arr = explode(',', $tag_str);
		foreach ($tag_arr as $tag_id) {
			$tag -> where('tag_id=%d', $tag_id) -> setDec('total_share');
		}
		return true;
	}

	/**
	 * 喜欢分享
	 */
	public function like($share_id = 0) {
		if($share_id == 0) $this -> error('参数错误');
		if(!is_login()) $this -> error('请先登录');
		$user_info = M('user_info');
		$like_list = $user_info -> where('user_id=%d', is_login()) -> getField('like_share');
		$like_array = explode(',', $like_list);
		//取得喜欢状态
		$flag = true;
		foreach ($like_array as $key => $value) {
			//如果喜欢过了，那么取消喜欢
			if($value == $share_id) {
				unset($like_array[$key]);//移出此元素
				$like_list = implode(',', $like_array);
				$flag = false;
				break;
			}
		}
		//“喜欢”操作
		if($flag) {
			$like_list .= ','.$share_id;
			$like_list = trim($like_list,',');
		}
		//保存数据
		$data['user_id'] = is_login();
		$data['like_share'] = $like_list;
		$result = $user_info -> save($data);
		if($result) {
			//碎片表被喜欢数更新
			$share = M('share');
			if($flag) $result = $share -> where('share_id=%d',$share_id) -> setInc('be_like');
			else $result = $share -> where('share_id=%d',$share_id) -> setDec('be_like');

			if($result) {
				$this -> success($flag ?"1":"0");
			} else {
				$this -> error('数据表更新出错,请联系管理员:'.$share -> getDbError());
			}
		} else {
			$this -> error("用户数据出错了,请联系管理员:".$user_info -> getDbError());
		}
	}

	/**
	 * 试试手气
	 */
	public function random() {
		$share = M('share');
		$share_id = $share -> order('Rand()') -> limit(1) -> getField('share_id');
		$this -> redirect('Shard/detail','id='.$share_id);
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
		$result = $comment -> add($data);//return了一个comment_id

		if($result) {
			$share = M('share');
			$share -> where('share_id=%d', $share_id) -> setInc('total_comments');
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
}


