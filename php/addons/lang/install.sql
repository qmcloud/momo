CREATE TABLE IF NOT EXISTS `__PREFIX__lang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文件目录',
  `raw_lang_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '原记录',
  `lang_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '改记录',
  `createtime` int(11) DEFAULT NULL COMMENT '修改时间',
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '状态:1=成功,0=失败',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
