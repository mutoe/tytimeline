<?php
//UC通信接口基类,抽象类，必须继承使用
namespace Ucenter\Api;
use Think\Controller;
abstract class Uc extends Controller{
    public $code;//code参数原始字符串
    public $action;//解析code得到的动作名
    public $error = NULL;
    public $post;//post数据
    public $get;//code解密后的数组，get参数
    /**
     * 初始化方法
     */
    public function _initialize(){
        $this->initConfig();//加载uc配置文件
        load("Ucenter.functions");//加载UC函数库
        load("Ucenter.XML");//加载UC XML类库
        $this->initConfig();//初始化UC应用配置
        $this->initRequest();//初始化请求
    }

    function initConfig(){
        require_cache(MODULE_PATH."Conf/uc.php");
        if(!defined('UC_API')) {
            exit('未发现uc配置文件，请确定配置文件位于'.MODULE_PATH."Conf/uc.php");
        }
    }
    /**
     * 解析请求
     * @return boolean
     */
    public function initRequest(){
        $code = @$_GET['code'];
	parse_str(_uc_authcode($code, 'DECODE', UC_KEY), $get);
	if(get_magic_quotes_gpc()) {
		$get = _uc_stripslashes($get);
	}
        if(empty($get)) {
		$this->error = '非法请求';
                return false;
	}
	$timestamp = time();
	if($timestamp - $get['time'] > 3600) {
		$this->error = '请求有效期已过';
                return false;
	}
        $this->get = $get;
	$this->code = $code;
	$this->action = parse_name($get['action'],'1');
        $this->post = xml_unserialize(file_get_contents('php://input'));
    }
    /**
     * 响应ucserver的通信请求，调用相应方法，输出最终结果并结束整个流程
     */
    public function response(){
        if($this->_before_response()){
            if ($this->error !== NULL) {
                exit($this->error);
            }
            if(!method_exists($this, $this->action)){
                $this->error = $this->action.'方法未定义';
                exit($this->error);
            }
            $response = call_user_func(array($this,$this->action),$this->get,$this->post);
        }
        if($this->_after_response($response)){
            exit($response);
        }
        exit('-1');
    }

    protected function _before_response(){
        return true;
    }

    protected function _after_response($response=""){
        return true;
    }

    public function test(){
        return '1';
    }


	public function deleteuser() {
		$get = $this->get;
		$user = M('user');
		$map['user_id']  = array('IN', $get['uid']);
		$result = $user -> where($map) -> delete();
		return 1;
	}

	public function renameuser() {
		$get = $this->get;
		$user = M('user');
		$data['nickname'] = strtolower($get['newusername']);
		$result = $user -> where('user_id=%d', $get['uid']) -> save($data);
		return 1;
	}

	public function updatepw() {
		$get = $this->get;
		$user = M('user');
		$data['password'] = md5($get['password']);
		$result = $user -> where('user_id=%d', $get['uid']) -> save($data);
		return 1;
	}

	public function synlogin() {
		$get = $this->get;
		$user = M('user');
		$member = $user -> where('user_id=%d', $get['uid']) -> field('user_id,nickname') -> find();

		if($member) {
			header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
			dsetcookie('Example_auth', authcode($member['user_id']."\t".$member['nickname'], 'ENCODE'), 86400 * 365);
		}
		return 1;
	}
	
	public function synlogout() {
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		dsetcookie('Example_auth', '', -86400 * 365);
	}
	
	public function updatebadwords() {
		$cachefile = APP_PATH.'Ucenter/Client/uc_client/data/cache/badwords.php';
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'badwords\'] = '.var_export($this->post, TRUE).";\r\n";
		file_put_contents($cachefile,$s);
		return 1;			
	}	
	
	public function updatehosts() {
		$cachefile = APP_PATH.'Ucenter/Client/uc_client/data/cache/hosts.php';
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'hosts\'] = '.var_export($this->post, TRUE).";\r\n";
		file_put_contents($cachefile,$s);
		fclose($fp);		
	}
	
	public function updateapps() {
		$cachefile = APP_PATH.'Ucenter/Client/uc_client/data/cache/apps.php';
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'apps\'] = '.var_export($this->post, TRUE).";\r\n";
		file_put_contents($cachefile,$s);
		return 1;		
	}	
	
	public function updateclient() {
		$cachefile = APP_PATH.'Ucenter/Client/uc_client/data/cache/setting.php';
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'setting\'] = '.var_export($this->post, TRUE).";\r\n";
		file_put_contents($cachefile,$s);
		return 1;
	}
	
}

