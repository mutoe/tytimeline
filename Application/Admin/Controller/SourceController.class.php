<?php
namespace Admin\Controller;
use Think\Controller;
class SourceController extends BaseController {

	public function index() {
		$this -> display();
	}

	/**
	 * 分享列表管理
	 */
	public function shard() {
		$share = M('share');
		$data = $share -> select();
		$this -> assign('data', $data);

		$this -> display();
	}

	/**
	 * 分享删除处理
	 */
	public function delShare($share_id = 0) {
		if($share_id == 0) $this -> error('参数有误');
		$share = M('share');
		$data = $share -> find($share_id);

		// 删除图像
		unlink('./Public/'. $data['savepath']. $data['savename']);
		unlink('./Public/'. $data['savepath']. 't_'. $data['savename']);

		// 删除数据
		$result = $share -> delete($share_id);

		if($result) {
			$this -> success();
		} else {
			$this -> error('删除失败');
		}
	}

	/**
	 * 分类列表管理
	 */
	public function catalog() {
		$catalog = M('catalog');
		$data = $catalog -> order('sort desc') -> select();
		$this -> assign('data', $data);

		$this -> display();
	}

	public function modifyCatalog($catalog_id = 0) {
		if(!$catalog_id) $this -> error("参数有误！");
		$catalog = M('catalog');
		$data = $catalog -> find($catalog_id);
		$this -> assign('data', $data);

		$this -> display('addCatalog');
	}

	public function submitCatalog() {
		$catalog = M('catalog');
		$data = I('post.');

		if($data['catalog_id']) {
			$result = $catalog -> save($data);
		} else {
			$result = $catalog -> add($data);
		}
		if($result !== flase) $this -> success();
		else $this -> error( $catalog -> getError() );
	}

	public function deleteCatalog($catalog_id = 0) {
		if(!$catalog_id) $this -> error("参数有误！");
		$catalog = M('catalog');
		$result = $catalog -> delete($catalog_id);
		if($result) {
			$this -> success();
		} else {
			$info = $catalog -> getError();
			$this -> error( $info );
		}
	}

	/**
	 * 标签列表管理
	 */
	public function tag($sort = 'tag_id') {
		$tag = M('tag');
		$sort .= ' desc';
		$data = $tag -> order($sort) -> select();
		$this -> assign('data', $data);

		$this -> display();
	}

	/**
	 * 删除标签操作
	 */
	public function delete_tag($tag_id = 0) {
		$tag_id == 0 && $this -> error('参数传递有误');
		$tag = M('tag');
		$result = $tag -> where('tag_id=%d',$tag_id) -> delete();
		if($result === false){ //因为返回值$result是返回删除的数据数 ，所以使用全等判断SQL语句失败
			$this -> error('数据库操作失败 :-( ');
		} else {
			$this -> success('删除成功');
		}
	}

	/**
	 * 轮播图列表管理
	 */
	public function banner() {
		$banner = M('banner');
		$b = $banner -> select();
		$this -> assign('data', $b);

		$this -> display();
	}

	/**
	 * 添加轮播图页面
	 */
	public function addBanner() {
		$this -> display();
	}

	/**
	 * 轮播图删除操作
	 */
	public function delBanner($banner_id = 0) {
		if($banner_id == 0) $this -> error('参数有误！');
		$banner = M('banner');
		$data = $banner -> find($banner_id);

		// 同步删除文件
		unlink('./Public/img/banner/'. $data['savename']);
		unlink('./Public/img/banner/t_'. $data['savename']);

		// 删除数据
		$result = $banner -> delete($banner_id);

		if($result) {
			$this -> success();
		} else {
			$this -> error('删除失败');
		}
	}

	/**
	 * 图片上传处理
	 */
	public function uploadBanner() {
		$config = array(
			'maxSize'		=> 5 * 1024 * 1024,	// 5M
			'exts'			=> array('jpg', 'gif', 'png', 'jpeg'),
			'saveExt'		=> 'jpg',
			'rootPath'	=> './Public/',
			'savePath'	=> 'img/banner/',
			'autoSub'		=> false,
    );
		$upload = new \Think\Upload($config);	// 实例化上传类
		// 上传文件
		$info = $upload -> uploadOne($_FILES['upbanner']);
		if (!$info) {	// 上传错误提示错误信息
			$info = $upload -> getError();
			$this -> error($info);
		} else {
			// 写入数据库
			$banner = M('banner');
			$data = array(
				'savename' => $info['savename'],
				'create_time' => time(),
				'create_user' => is_login(),
				'status' => '-1',
			);
			$result = $banner -> add( $data );//return $banner_id
			if($result === false) {
				$info = $banner -> getError();
				$this -> error( $info );
			} else {
				$this -> success( $result );//返回banner_id
			}
		}
	}

	public function bannerFun($banner_id = 0) {
		$banner = M('banner');
		$data = $banner -> find($banner_id);
		if(I('post.w') > 0) {
			$user_id = I('post.source', 0) ? I('post.source') : is_login();

			$image = new \Think\Image();
			$image -> open('./public/img/banner/'. $data['savename']);

			// 缩放处理
			$width = $image -> width();	//获取原始图片宽度
			$w = I('post.w');	//获取前台图片宽度
			$f = $width / $w;	//计算缩放比例
			$x = I('post.x') * $f;
			$y = I('post.y') * $f;
			$w = I('post.w') * $f;
			$h = I('post.h') * $f;

			// 裁切处理
			$image -> crop($w, $h, $x, $y) -> save('./public/img/banner/'. $data['savename'] );

			// 重新打开图片生成成品图片处理
			$image -> open('./Public/img/banner/'. $data['savename']);
			$image -> thumb(1920, 817) -> save('./public/img/banner/'. $data['savename'] );	// desktop用大图
			$image -> thumb(960, 408) -> save('./public/img/banner/t_'. $data['savename'] );	// mobile设备用小图

			// 嵌入水印
	    $image -> open('./public/img/banner/'. $data['savename'] );
			$image -> text( C('WATERMARK_TEXT') ,'./Public/fonts/msyhbd.ttf', 14, '#ffffff40', 8 , -50 );
			$image -> text( " @". get_nickname($user_id) ,'./Public/fonts/msyhbd.ttf', 20, '#ffffff40', 8 , -20 );
			$image -> save('./public/img/banner/'. $data['savename'] );

			// 嵌入水印（小图）
	    $image -> open('./public/img/banner/t_'. $data['savename'] );
			$image -> text( C('WATERMARK_TEXT') ,'./Public/fonts/msyh.ttf', 10, '#ffffff40', 8 , -30 );
			$image -> text( " @". get_nickname($user_id) ,'./Public/fonts/msyh.ttf', 12, '#ffffff20', 8 , -10 );
			$image -> save('./public/img/banner/t_'. $data['savename'] );

			//更改轮播图状态
			$banner -> where('banner_id=%d', $banner_id) -> setField('source', $user_id);
			$banner -> where('banner_id=%d', $banner_id) -> setField('status', 1);

			$this -> success('成功了', U('banner'));
		} else {
			if($banner_id == 0) $this -> error('出错了:参数有误!');
			if($data['status'] != '-1') $this -> error('出错了:该图片已被处理或不存在!');
			$this -> assign('data', $data);

			$this -> display();
		}
	}

	public function bannerModi($banner_id = 0) {
		if(!$banner_id) $this -> error('参数错误！');
		$banner = M('banner');
		$data = $banner -> find($banner_id);
		$this -> assign('data', $data);

		$this -> display();
	}

	public function bannerModiSubmit($banner_id = 0) {
		if(!$banner_id) $this -> error('参数错误！');
		$banner = M('banner');
		$data = I('post.');
		$data['banner_id'] = $banner_id;
		$result = $banner -> save($data);
		if($result !== false) $this -> success("修改成功");
		else $this -> error( $banner -> getError() );
	}
}
