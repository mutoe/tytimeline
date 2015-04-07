<?php
namespace Home\Model;
use Think\Model;

class UserModel extends Model {

	protected $_auto = array (
		array('group','6'), // 新增的时候把用户组设置为普通会员
		array('password', 'md5', 3, 'function') , // 对password字段在新增和编辑的时候使md5函数处理
		array('create_time','time',1,'function'),
		array('lastlogin_time','time',3,'function'),
	);

	protected $_validate = array(
		array('nickname','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		array('email','','该邮箱已经被注册！',0,'unique',1), // 在新增的时候验证name字段是否唯一
	);

}