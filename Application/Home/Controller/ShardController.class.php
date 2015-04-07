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
		//获取分享详情
		$data = $share -> where('share_id=%d',$id) -> find();
		if(!$data) $this -> error('参数错误');
		$this -> assign('data', $data);
		//获取近期分享 4个
		$near = $share -> where('user_id=%d', $data['user_id']) -> order('create_time desc') -> limit(0,4) -> field('savepath,savename,detail,share_id') -> select();
		$this -> assign('near', $near);
		//获取用户详情
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
			'maxSize' => 3145728,
			'exts'=>array('jpg', 'gif', 'png', 'jpeg'),
			'rootPath'=>'./Public/',
			'savePath'=>'uploads/',
			'autoSub' => true,
			'subName' => array('date','Ym'),
    );
		$upload = new \Think\Upload($config); // 实例化上传类
		// 上传文件
		$info = $upload -> uploadOne($_FILES['addimg']);
		if (!$info) {// 上传错误提示错误信息
			$this -> error($upload -> getError().'1',true);
		} else {// 上传成功
			// 写入数据库
			$user_id = is_login();
			$share = M('share');
			$data = array(
				'user_id' => $user_id,
				'savepath' => $info['savepath'],
				'savename' => $info['savename'],
				'time' => time(),
				'create_time' => time(),
				'month' => date('Ym', time()),
			);
			$result = $share -> add($data);//return $share_id

			if($result) {
				//总分享数自增
				$user_info = M('user_info');
				$user_info -> where('user_id=%d',$user_id) -> setInc('total_share');
				//初始化image类
		    $image = new \Think\Image();
		    $image -> open('./Public/'.$info['savepath'].$info['savename']);
				//计算宽高
				$size = $image -> size();
				$data2['width'] = $size[0];
				$data2['height'] = $size[1];
				$share -> where('share_id=%d', $result) -> save($data2);

		    // 生成缩略图
		    $image -> thumb(245, 2500) -> save('./Public/'.$info['savepath'].'t_'.$info['savename']);

				$this -> success($result);//返回share_id
			} else {
				unlink('./Public/'.$info['savepath'].$info['savename']);
				$this -> error('数据写入失败，请联系管理员；',true);
			}
		}
	}

	/**
	 * 图片信息修改页面
	 */
	public function modi($share_id = 0) {
		$temp = M('share');
		$user_id = $temp -> where('share_id=%d',$share_id) -> getField('user_id');
		//检查删除权限
		if($user_id != is_login() and !get_auth('delete')) {//如果不是自己的分享并且没有删除权限
			$this -> error('非法操作！');
			return false;
		}
		if(!I('param.time', 0)) {
			//如果不是提交请求
			$share = M('share');
			$data = $share -> where('share_id=%d',$share_id) -> find();
			$this -> assign('data',$data);
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
		if(!IS_AJAX) $this -> error('非法请求！');
		$share = M('share');
		$data = $share -> where('share_id=%d',$share_id) -> find();
		if(!$data) $this -> error('该条数据不存在！');
		//检查删除权限
		if($data['user_id'] != is_login() and !get_auth('delete')) $this -> error('非法操作！');//如果不是自己的分享并且没有删除权限

		$address = './Public/'.$data['savepath'].$data['savename'];
		$address_t = './Public/'.$data['savepath'].'t_'.$data['savename'];
		$result = file_exists($address) ? unlink($address) : true;
		$result = file_exists($address_t) ? unlink($address_t) : true;
		if($result) {
			//删除数据
			$result = $share -> delete();
			//总分享数自减
			$user_info = M('user_info');
			$user_info -> where('user_id=%d',$data['user_id']) -> setDec('total_share');

			if($result) {
				$this -> success('删除成功');
			} else {
				$this -> error('删除失败，请联系管理员.删除数据失败');
			}
		} else {
			$this -> error('删除失败，请联系管理员.移除资源出错');
		}
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
}


