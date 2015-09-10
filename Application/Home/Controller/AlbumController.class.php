<?php
namespace Home\Controller;
use Think\Controller;

class AlbumController extends BaseController {

  /**
   * 新建一个图集
   */
  public function create() {
    $title = trim(I('post.title'));
    if(mb_strlen($title) < 1) {
      $this -> error('请输入主题');
    }
    $user_id = is_login();
    $album = M('album');
    $count = $album -> where('user_id=%d and title="%s"', array($user_id, $title)) -> count();
    if($count) {
      $this -> error('已经存在的名字');
    }
    //$this -> error(I('post.title'));
    $data['title'] = $title;
    $data['create_time'] = time();
    $data['user_id'] = is_login();
    $result = $album -> add($data);
    if($result) {
      $result = array('album_id' => $result, 'title' => $title);
      $this -> success(json_encode($result));
    } else {
      $this -> error('数据创建失败');
    }
  }

}
