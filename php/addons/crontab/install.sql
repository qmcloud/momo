CREATE TABLE IF NOT EXISTS `__PREFIX__crontab` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(10) NOT NULL DEFAULT '' COMMENT '事件类型',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '事件标题',
  `content` text NOT NULL COMMENT '事件内容',
  `schedule` varchar(100) NOT NULL DEFAULT '' COMMENT 'Crontab格式',
  `sleep` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '延迟秒数执行',
  `maximums` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最大执行次数 0为不限',
  `executes` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '已经执行的次数',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `begintime` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `endtime` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `executetime` bigint(16) DEFAULT NULL COMMENT '最后执行时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('completed','expired','hidden','normal') NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='定时任务表';

BEGIN;
INSERT INTO `__PREFIX__crontab` (`id`, `type`, `title`, `content`, `schedule`, `sleep`, `maximums`, `executes`, `createtime`, `updatetime`, `begintime`, `endtime`, `executetime`, `weigh`, `status`) VALUES
(1, 'url', '请求百度', 'https://www.baidu.com', '* * * * *', 0, 0, 0, 1497070825, 1501253101, 1483200000, 1830268800, 1501253101, 1, 'normal'),
(2, 'sql', '查询一条SQL', 'SELECT 1;', '* * * * *', 0, 0, 0, 1497071095, 1501253101, 1483200000, 1830268800, 1501253101, 2, 'normal');
COMMIT;

CREATE TABLE IF NOT EXISTS `__PREFIX__crontab_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `crontab_id` int(10) DEFAULT NULL COMMENT '任务ID',
  `executetime` bigint(16) DEFAULT NULL COMMENT '执行时间',
  `completetime` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `content` text COMMENT '执行结果',
  `status` enum('success','failure') DEFAULT 'failure' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `crontab_id` (`crontab_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='定时任务日志表';
