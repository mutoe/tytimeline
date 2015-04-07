-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-04-07 16:37:04
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tytimeline`
--

-- --------------------------------------------------------

--
-- 表的结构 `tytl_share`
--

CREATE TABLE IF NOT EXISTS `tytl_share` (
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
  PRIMARY KEY (`share_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `tytl_tag`
--

CREATE TABLE IF NOT EXISTS `tytl_tag` (
  `tag_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(12) NOT NULL,
  `create_user` int(8) unsigned NOT NULL,
  `create_time` int(12) NOT NULL,
  `total_share` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `tytl_tag`
--

-- --------------------------------------------------------

--
-- 表的结构 `tytl_user`
--

CREATE TABLE IF NOT EXISTS `tytl_user` (
  `user_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(80) NOT NULL,
  `nickname` char(12) NOT NULL,
  `password` varchar(64) NOT NULL,
  `create_time` int(12) unsigned NOT NULL,
  `lastlogin_time` int(12) unsigned NOT NULL,
  `group_id` tinyint(4) unsigned NOT NULL DEFAULT '6',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `login_times` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '登陆次数',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `tytl_user`
--

INSERT INTO `tytl_user` (`user_id`, `email`, `nickname`, `password`, `create_time`, `lastlogin_time`, `group_id`, `level`, `login_times`) VALUES
(1, 'admin@admin.com', 'admin', 'e3ceb5881a0a1fdaad01296d7554868d', 1425980718, 1426081891, 1, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `tytl_user_group`
--

CREATE TABLE IF NOT EXISTS `tytl_user_group` (
  `group_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) DEFAULT NULL,
  `auth` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `tytl_user_group`
--

INSERT INTO `tytl_user_group` (`group_id`, `name`, `auth`) VALUES
(1, '超级管理员', '1111111111'),
(2, '管理员', '1111111111'),
(3, '网站编辑', '1111111100'),
(4, '时光达人', '1111100000'),
(5, '贵宾', '1111100000'),
(6, '会员', '1111100000'),
(7, '僵尸', '1111100000'),
(8, '黑名单', '1111100000');

-- --------------------------------------------------------

--
-- 表的结构 `tytl_user_info`
--

CREATE TABLE IF NOT EXISTS `tytl_user_info` (
  `user_id` int(8) unsigned NOT NULL DEFAULT '0',
  `be_like` int(11) DEFAULT '0',
  `detail` varchar(255) DEFAULT NULL,
  `like` text,
  `like_share` text,
  `total_share` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tytl_user_info`
--

INSERT INTO `tytl_user_info` (`user_id`, `be_like`, `detail`, `like`, `like_share`, `total_share`) VALUES
(1, 0, '', NULL, NULL, 0);

