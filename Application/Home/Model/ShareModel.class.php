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
		//array('share_id','check_self','你无法这么做',2,'callback'),//TODO:有点问题。。。待修复
		array('tag_id','check_tag','标签格式有误',2,'callback'),
		array('detail','0,255','心情超出最大字符限制',2,'length'),
	);

	/**
	 * 检查是否修改自己的分享
	 */
	protected function check_self($share_id) {
		$share = M('share');
		$user_id = $share -> where('share_id=%d',$share_id) -> getField($user_id);
		if(get_auth('modify')) return true;
		return $user_id == is_login()? true: false;
	}

	/**
	 * 将文字字符串标签(用' '分割)替换成tag_id字符串(用,分隔) - 自动完成方法
	 * @param string 文字字符串标签(用' '分割)
	 * @return string id标签字符串(用,分隔)
	 * @author mt
	 */
	protected function tag_fun($tag_org) {
		if($tag_org == '') return null;
		//return $tag_org;
		$tag = M('tag');
		$tag_org = trim($tag_org, ' ');
		$tag_arr = preg_split("/\s/", $tag_org);
		$result = '';
		foreach ($tag_arr as $value) {
			$value = trim($value, ' ');
			if($value == '') continue;
			$tgid = $tag -> where("tag_name='%s'",$value) -> getField('tag_id');
			if($tgid) {
				//如果存在该标签
				$result .= $tgid.',';
				$total_share = $tag -> where("tag_name='%s'",$value) -> getField('total_share');
				$data['total_share'] = $total_share++;
				$tag -> where("tag_name='%s'",$value) -> save($data);
			} else {
				//新建标签
				$data['tag_name'] = $value;
				$data['create_user'] = I('session.user_id');
				$data['create_time'] = time();
				$data['total_share'] = 1; //新增一个分享
				$add_tag = $tag -> add($data);
				$result .= $add_tag.',';
			}
		}
		return rtrim($result, ',');
	}

	/**
	 * 验证标签格式方法 - 字段自动验证方法
	 * @param string 原始文字标签字符串
	 * @author mt
	 */
	protected function check_tag($tag_org) {
		// 如果没有输入，则通过验证
		if($tag_org == '') return true;
		// 如果去除所有分隔符号的结果为空，则验证失败。如“ , ,,”
		$match = preg_replace("/\s+/",'', $tag_org);
		if($match == '') return false;
		//去除首尾空格
		$tag_org = trim($tag_org, ' ');
		//匹配中文逗号作为分隔符
		$tag_arr = preg_split("/\s+/", $tag_org);
		//当超过 7 个标签不通过验证
		if(count($tag_arr) > 7) return false;
		$result = '';
		//每个标签超过 12 个字符不通过匹配
		foreach ($tag_arr as $value) {
			$value = trim($value, ' ');
			if(strlen($value) > 12) return false;
		}
		return true;
	}
}