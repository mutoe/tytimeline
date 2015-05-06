<?php
namespace Home\Model;
use Think\Model;

class ShareModel extends Model {
	protected $_auto = array (
		array('tag_id','tag_fun',3,'callback'),
		array('detail','ignore'),
		array('time','strtotime',3,'function'),
	);
	protected $_validate = array(
		array('tag_id','check_tag','标签格式有误',2,'callback'),
		array('detail','0,255','心情超出最大字符限制',2,'length'),
	);

	/**
	 * 将文字字符串标签(用' '分割)替换成tag_id字符串(用,分隔) - 自动完成方法
	 * @param json json串形式的tag列表
	 * @return string id标签json串
	 * @author mt
	 */
	protected function tag_fun($tag_json) {
		$data = explode(',', $tag_json);

		// 去除首尾空格
		foreach ($data as $value) $value = trim($value, ' ');
		// 去重
		$data = array_unique($data);

		$tag = M('tag');
		$user_id = is_login();
		$result = array();
		foreach ($data as $value) {
			$tgid = $tag -> where("tag_name='%s'",$value) -> getField('tag_id');
			if($tgid) {
				//如果存在该标签
				array_push($result, $tgid);
				$tag -> where("tag_id=%d", $tgid) -> setInc('total_share');	// 标签分享数自增
			} else {
				//新建标签
				$data['tag_name'] = $value;
				$data['create_user'] = $user_id;
				$data['create_time'] = time();
				$data['total_share'] = 1; //新增一个分享
				$tgid = $tag -> add($data);
				array_push($result, $tgid);
			}
		}
		return json_encode( $result );
	}

	/**
	 * 验证标签格式方法 - 字段自动验证方法
	 * @param json json串形式的tag列表
	 * @author mt
	 */
	protected function check_tag($tag_json) {
		$data = json_decode($tag_json);
		if( count($data) > 7 ) return false;
		foreach ($data as $value) {
			if( mb_strlen($value, 'utf8') > 12 ) return false;
		}
		return true;
	}
}