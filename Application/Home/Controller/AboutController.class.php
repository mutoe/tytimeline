<?php
namespace Home\Controller;
use Think\Controller;

class AboutController extends BaseController {
  public function feedback() {
    if(!IS_AJAX) {
      $this -> display();
    } else {
      $data = I('post.');
      $data['create_time'] = time();
      $data['create_ip'] = get_client_ip();
      $data['create_user'] = is_login();

      $feedback = M('feedback');
      $result = $feedback -> add($data);
      if($result) $this -> success();
      else $this -> error($feedback -> getError());
    }
  }

}

