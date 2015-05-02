/*
Navicat MySQL Data Transfer

Source Server         : MySQL
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : tytimeline

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-05-02 16:31:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tytl_banner
-- ----------------------------
DROP TABLE IF EXISTS `tytl_banner`;
CREATE TABLE `tytl_banner` (
  `banner_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(11) NOT NULL COMMENT '来源用户id,-1系统',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:正常,0:隐藏',
  `sort` smallint(6) NOT NULL DEFAULT '0',
  `savename` varchar(255) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tytl_catalog
-- ----------------------------
DROP TABLE IF EXISTS `tytl_catalog`;
CREATE TABLE `tytl_catalog` (
  `catalog_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `catalog_name` varchar(20) DEFAULT NULL,
  `total_share` int(8) unsigned NOT NULL DEFAULT '0',
  `sort` int(4) NOT NULL DEFAULT '0',
  `status` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`catalog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_catalog
-- ----------------------------
INSERT INTO `tytl_catalog` VALUES ('1', '无题', '0', '1', '1');
INSERT INTO `tytl_catalog` VALUES ('2', '生活', '0', '0', '1');
INSERT INTO `tytl_catalog` VALUES ('3', '游玩', '0', '0', '1');
INSERT INTO `tytl_catalog` VALUES ('4', '创意', '0', '0', '1');
INSERT INTO `tytl_catalog` VALUES ('5', '人物', '0', '0', '1');
INSERT INTO `tytl_catalog` VALUES ('6', '动植物', '0', '0', '1');
INSERT INTO `tytl_catalog` VALUES ('7', '其他', '0', '-100', '1');
INSERT INTO `tytl_catalog` VALUES ('8', '影视', '0', '0', '1');
INSERT INTO `tytl_catalog` VALUES ('9', '交大风光', '0', '100', '1');
INSERT INTO `tytl_catalog` VALUES ('10', '美食', '0', '0', '1');

-- ----------------------------
-- Table structure for tytl_comment
-- ----------------------------
DROP TABLE IF EXISTS `tytl_comment`;
CREATE TABLE `tytl_comment` (
  `comment_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `share_id` int(11) unsigned NOT NULL,
  `user_id` int(8) unsigned NOT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `create_time` int(12) DEFAULT NULL,
  `admire` int(8) DEFAULT '0',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_comment
-- ----------------------------

-- ----------------------------
-- Table structure for tytl_config
-- ----------------------------
DROP TABLE IF EXISTS `tytl_config`;
CREATE TABLE `tytl_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `attr` tinyint(4) NOT NULL DEFAULT '1' COMMENT '属性\r\n0:必须\r\n1:可写 (default)\r\n',
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_config
-- ----------------------------
INSERT INTO `tytl_config` VALUES ('1', 'SITE_NAME_EN', 'TY timeline', '0', '站点名称英文');
INSERT INTO `tytl_config` VALUES ('2', 'SITE_NAME_CN', '天佑时光轴', '0', '站点名称中文');
INSERT INTO `tytl_config` VALUES ('3', 'SITE_VER', '0.1.8 alpha', '0', '站点版本');
INSERT INTO `tytl_config` VALUES ('4', 'NOTICE_FRONT', '', '0', '前台公告');
INSERT INTO `tytl_config` VALUES ('5', 'NOTICE_BACK', '欢迎访问天佑时光轴后台管理系统！', '0', '后台公告');
INSERT INTO `tytl_config` VALUES ('6', 'WATERMARK_TEXT', 'TY timeline', '0', '水印文字');

-- ----------------------------
-- Table structure for tytl_share
-- ----------------------------
DROP TABLE IF EXISTS `tytl_share`;
CREATE TABLE `tytl_share` (
  `share_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` text,
  `detail` varchar(255) DEFAULT NULL,
  `time` int(12) DEFAULT NULL,
  `month` int(6) unsigned DEFAULT '201501',
  `create_time` int(12) DEFAULT NULL,
  `user_id` int(8) unsigned DEFAULT NULL,
  `savepath` varchar(255) DEFAULT NULL,
  `savename` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `be_like` int(11) unsigned DEFAULT '0',
  `click` int(11) unsigned DEFAULT '0',
  `width` int(6) DEFAULT NULL,
  `height` int(6) DEFAULT NULL,
  `total_comments` int(8) unsigned DEFAULT '0',
  `catalog_id` int(4) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`share_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for tytl_tag
-- ----------------------------
DROP TABLE IF EXISTS `tytl_tag`;
CREATE TABLE `tytl_tag` (
  `tag_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(12) NOT NULL,
  `create_user` int(8) unsigned NOT NULL,
  `create_time` int(12) NOT NULL,
  `total_share` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_tag
-- ----------------------------

-- ----------------------------
-- Table structure for tytl_user
-- ----------------------------
DROP TABLE IF EXISTS `tytl_user`;
CREATE TABLE `tytl_user` (
  `user_id` int(8) unsigned NOT NULL,
  `email` varchar(80) NOT NULL,
  `nickname` char(12) NOT NULL,
  `password` varchar(64) NOT NULL,
  `create_time` int(12) unsigned NOT NULL,
  `lastlogin_time` int(12) unsigned NOT NULL,
  `group_id` tinyint(4) unsigned NOT NULL DEFAULT '6',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `login_times` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `lastlogin_ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_user
-- ----------------------------
INSERT INTO `tytl_user` VALUES ('1', 'admin@admin.com', 'admin', 'e3ceb5881a0a1fdaad01296d7554868d', '1425980718', '1428938825', '1', '0', '1', '10.1.59.152');
INSERT INTO `tytl_user` VALUES ('2', '332019319@qq.com', 'mt', 'e3ceb5881a0a1fdaad01296d7554868d', '1425980801', '1430545537', '2', '0', '13', '127.0.0.1');

-- ----------------------------
-- Table structure for tytl_user_group
-- ----------------------------
DROP TABLE IF EXISTS `tytl_user_group`;
CREATE TABLE `tytl_user_group` (
  `group_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) DEFAULT NULL,
  `auth` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_user_group
-- ----------------------------
INSERT INTO `tytl_user_group` VALUES ('1', '超级管理员', '1111111111');
INSERT INTO `tytl_user_group` VALUES ('2', '管理员', '1111111111');
INSERT INTO `tytl_user_group` VALUES ('3', '网站编辑', '1111111100');
INSERT INTO `tytl_user_group` VALUES ('4', '时光达人', '1111100000');
INSERT INTO `tytl_user_group` VALUES ('5', '贵宾', '1111100000');
INSERT INTO `tytl_user_group` VALUES ('6', '会员', '1111100000');
INSERT INTO `tytl_user_group` VALUES ('7', '僵尸', '1111100000');
INSERT INTO `tytl_user_group` VALUES ('8', '黑名单', '1111100000');

-- ----------------------------
-- Table structure for tytl_user_info
-- ----------------------------
DROP TABLE IF EXISTS `tytl_user_info`;
CREATE TABLE `tytl_user_info` (
  `user_id` int(8) unsigned NOT NULL DEFAULT '0',
  `be_like` int(11) DEFAULT '0',
  `detail` varchar(255) DEFAULT NULL,
  `like` text,
  `like_share` text,
  `total_share` int(11) unsigned DEFAULT '0',
  `gander` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tytl_user_info
-- ----------------------------
INSERT INTO `tytl_user_info` VALUES ('1', '0', null, null, null, '0', null);
INSERT INTO `tytl_user_info` VALUES ('2', '0', '', null, null, '0', null);
