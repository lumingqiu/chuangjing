set names utf8;
#用户信息表
CREATE TABLE `cj_user`
(
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_account` varchar(64) NOT NULL COMMENT '用户账号',
    `user_password` varchar(64) NOT NULL COMMENT '用户密码',
    `user_name` varchar(64) NOT NULL DEFAULT '' COMMENT '用名称',
	`user_level` varchar(64) NOT NULL DEFAULT '' COMMENT '用户代理等级1管理员',
	`parent_user_id` INT UNSIGNED NOT NULL DEFAULT '1'  COMMENT '用户上级账号id，对应cj_user表user_id',
	`user_limit` varchar(64) NOT NULL DEFAULT '' COMMENT '账户登记权限,0超级管理员,1业务员,2渠道',
	`user_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态，0表示正常,1表示冻结,2表示删除',
	`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`user_id`),
	UNIQUE INDEX idx_search(`user_account`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- #用户关系表
-- CREATE TABLE `cj_user_relation`
-- (
-- 	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
-- 	`user_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户账号id，对应cj_user表user_id',
-- 	`parent_user_id` INT UNSIGNED NOT NULL DEFAULT '0'  COMMENT '用户上级账号id，对应cj_user表user_id',
-- 	PRIMARY KEY (`id`),
-- 	UNIQUE INDEX idx_search(`user_id`)
-- )ENGINE=InnoDB DEFAULT CHARSET=utf8;

#广告应用信息表
CREATE TABLE `cj_ad_app`
(
	`app_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`ad_name` varchar(64) NOT NULL COMMENT '广告主名称',
    `ad_salesman_id` INT NOT NULL COMMENT '业务员id，可以是超级管理员',
    `add_channel_id` INT NOT NULL COMMENT '渠道id，可以是超级管理员',
	`add_url_type` INT NOT NULL COMMENT '类型id，预留1:小点击',
	`app_name` varchar(64) NOT NULL COMMENT '应用名称',
	`max_activate_num` INT NOT NULL COMMENT '一天上报最大激活数，第二天重新开启',
	`in_price` float NOT NULL DEFAULT '0' COMMENT '进价',
	`out_price` float NOT NULL DEFAULT '0' COMMENT '出价',
	`channel_activate` float NOT NULL DEFAULT '0' COMMENT '渠道激活率',
	`app_unique_id` varchar(64)  NOT NULL DEFAULT '0' COMMENT 'app唯一标识',
	`ad_call_url` text NOT NULL COMMENT '广告主通知地址',
	`ad_call_type` INT NOT NULL  DEFAULT '1' COMMENT '1:get,2:post',
	`ad_appstore_url` text NOT NULL COMMENT 'APPSTORE地址',
	`ad_callback_url` text NOT NULL COMMENT '渠道回调地址',
	`ad_joint_type` INT(2) NOT NULL DEFAULT '1' COMMENT '对接方式 1:S2S,2:302重定向',
	`ad_platform_id` INT(2) NOT NULL DEFAULT '1' COMMENT '平台id，1iphone,2安卓',
	`ad_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态，0表示正常,1表示暂停,2表示删除',
	`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`app_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

#链接点击记录
CREATE TABLE `cj_ad_click_log`
(
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`app_id` INT UNSIGNED NOT NULL COMMENT "对应app投放驱动生成的id",
	`user_idfa` varchar(255) NOT NULL DEFAULT '0' COMMENT '终端设备idfa',
	`user_ip` varchar(64) NOT NULL DEFAULT '0' COMMENT '用户点击的ip',
	`user_ua` varchar(255)  DEFAULT '0' COMMENT '用户userAgent',
	`activate_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '激活状态，0表示未激活,1表示激活',
	`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`activate_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX ad_app_id("app_id")
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

#用户数据
insert into cj_user (`user_id`,`user_account`,`user_password`,`user_name`,`user_level`,`user_limit`,`user_state`) values(1,'admin',md5(123456),'管理员','1','0','0');

#insert into cj_user_relation(`user_id`,`parent_user_id`) values(1,1);