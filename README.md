
## 天佑时光轴 TianYou Timeline ##

Update Log
====================
----
[0.1.7.150422a]
----
### add ###
* 增加了分类表 `tytl_catalog` 和分享表的分类字段

		tytl_catalog:
			catalog_id unsigned int(4) pk,
			catalog_name varchar(20),
			sort int(4),
			status int(4),//分类状态，默认值1
			total_share int(8);
		tytl_share:
			catalog_id unsigned int(4);
* 增加了分类页面
* 增加了各分享的分类信息

### fixed ###
* 修复了IE下搜索框的大小问题

----
[0.1.6.150415a]
----
### add ###
* 增加了用户头像
* 增加了用户详情页面
* 增加了判断是否移动端的方法

### fixed ###
* 标签输入框更改为 `tagsinput` 插件
* 优化了标签页面，使用了 `wookmark` 的 flite 样式
* 优化了404页面

----
[0.1.5.150411a]
----
### add ###
* 增加了标签详情页面
* 增加了登陆IP字段 `lastlogin_ip` 性别字段 `gander` 分享的总评论字段 `total_comments` 和评论表 `tytl_comment`

		tytl_user:
			lastlogin_ip varchar(255);
		tytl_user_info:
			gander varchar(255);
		tytl_share:
			total_comments int(8); //总评论数
		tytl_comment: //新表
			comment_id unsigned int(12),
			share_id unsigned int(11),
			user_id unsigned int(8),
			detail varchar(255),
			create_time int(12),
			admire int(8); //被赞数
* 增加了图片的点击量自增
* 增加了站点的 `logo` 和 `favicon` ，感谢 **郭书昊** 提供的素材

### fixed ###
* **前端css框架更新至 `amaze ui 2.3.0`**
* **重构了首页瀑布流，改用 `wookmark` 样式**
* 修复了IE下的一些布局BUG
* 修复了未登录时查看分享详情，页面会报错
* 修复了删除分享时 `total_share` 字段的处理
* 修复了广场页面达人板块的用户组标签颜色错误显示
* 优化了按比例计算高度的方法 `get_t_height()`
* 优化了取得分享“喜欢”状态的方法 `get_like_status`
* 优化了取得操作权限的方法 `get_auth()` 增加第三个id参数为辅助判断参数
* 优化了广场页面视觉样式为 `wookmark` 瀑布流

### delete ###

* **移除了多说评论插件，替换为原生评论系统**

----
[0.1.4.150407a]
----
**内部测试版本**

### add ###

* 增加了创建分享时的宽高计算
* 增加了分享的宽高，数据库增加字段：

		tytl_share:
			width int(6),
			height int(6);
* 增加了以彩色的badge方式显示tag

### fixed ###

* 修复了访问网址share_id为不存在的项时会出现不友好的错误提示
* 修复了 `$this -> error();` 不会显示对应的错误提示信息
* 修正了上传分享时 `total_share` 字段自增，以及删除时的自减
* 优化了广场页面以及tag页面
* 优化了首页页脚的显示方法
* 进一步优化了时光轴页面

----
[0.1.3.150405]
----
### add ####

* 增加了修改分享信息的功能
* 增加了操作权限的判断

### fixed ###

* **修改了时光轴显示方法**
* 优化了分享详情页面
* 修改了标签分隔符为空格

----
[0.1.2.150402]
----
### add ###

* 增加了“试试手气”功能
* 增加了“广场”页面
* 增加了“标签”页面
* 增加了“喜欢”功能
* 增加了多说评论插件
* 增加了后台管理页面

### fixed ###

* **修改了时光轴页面，创意源自 `GBtags.com`**
* 修复了注册用户时没有初始化用户信息表`tytl_user_info`

----
[0.1.1.150215]
----
### add ###

* 增加了注册页面自动验证长度以及有效性
* 增加了修改分享信息页面自动处理以及验证有效性
* 增加了首页分享的工具条
* 增加了首页瀑布流布局
* 增加了首页顶部导航栏

### fixed ###

* 修复了首页在某些情况下出现横向滚动条
* 修改了图片分享方式为ajax处理

----
[0.1.0.150201]
----
### init ###

* 初步建立了前台页面以及后台部分页面
