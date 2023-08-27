
CREATE TABLE IF NOT EXISTS `__PREFIX__third` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '会员ID',
  `platform` varchar(30) DEFAULT '' COMMENT '第三方应用',
  `apptype` varchar(50) DEFAULT '' COMMENT '应用类型',
  `unionid` varchar(100) DEFAULT '' COMMENT '第三方UNIONID',
  `openid` varchar(100) DEFAULT '' COMMENT '第三方OPENID',
  `openname` varchar(100) DEFAULT '' COMMENT '第三方会员昵称',
  `access_token` varchar(255) NULL DEFAULT '' COMMENT 'AccessToken',
  `refresh_token` varchar(255) DEFAULT 'RefreshToken',
  `expires_in` int(10) unsigned DEFAULT '0' COMMENT '有效期',
  `createtime` bigint(16) unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) unsigned DEFAULT NULL COMMENT '更新时间',
  `logintime` bigint(16) unsigned DEFAULT NULL COMMENT '登录时间',
  `expiretime` bigint(16) unsigned DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform` (`platform`,`openid`),
  KEY `user_id` (`user_id`,`platform`),
  KEY `unionid` (`platform`,`unionid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='第三方登录表';

ALTER TABLE `__PREFIX__third` ADD COLUMN `apptype` varchar(50) NULL DEFAULT '' COMMENT '应用类型' AFTER `platform`;

ALTER TABLE `__PREFIX__third` ADD COLUMN `unionid` varchar(100) NULL DEFAULT '' COMMENT '第三方UnionID' AFTER `apptype`;
ALTER TABLE `__PREFIX__third` ADD INDEX `unionid`(`platform`, `unionid`);

ALTER TABLE `__PREFIX__third` CHARACTER SET = utf8mb4, COLLATE = utf8mb4_general_ci;
ALTER TABLE `__PREFIX__third` MODIFY COLUMN `openname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '第三方会员昵称' AFTER `unionid`;

