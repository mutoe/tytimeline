<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class BaseController extends CommonController {

	public function _initialize() {
		// 如果没有登陆就踢回
		if(!is_login()) {
			$this -> error('你还没有登陆', U('Home/Login/index'));
		} elseif(!is_admin()) {
			$this -> error('你没有权力这么做', U('Home/Index/index'));
		}
	}

	public function clearCache($dir = "./Application/Runtime"){
		if(!IS_AJAX) $this -> error("非法操作！");
		$auth = substr($dir,0,21);
		if($auth != "./Application/Runtime") $this -> error("非法操作！");
	  //先删除目录下的文件：
	  $dh = opendir($dir);
	  while ($file = readdir($dh)) {
	    if($file != "." && $file != "..") {
	    	$fullpath = $dir. "/" .$file;
	    	if(!is_dir($fullpath)) {
	      	unlink($fullpath);
	      } else {
	      	$this -> clearCache($fullpath);
	      }
	    }
	  }
	  closedir($dh);
	  rmdir($dir);
		if(rmdir("./Application/Runtime")) {
	    $this -> success();
	  }
	}

  /**
   * 用户反馈处理方法
   */
  public function handleFeedbackResult($result, $feedback_id = 0) {
    if(!is_admin() or !IS_AJAX or $feedback_id == 0) $this -> error("非法请求！");
    $feedback = M('feedback');
    $data['feedback_id'] = $feedback_id;
    $data['handle_user'] = is_login();
    $data['handle_time'] = time();
    $data['status'] = $result == "true" ? 0 : -1;
    $feedback -> save($data);
    $this -> success();
  }

}
