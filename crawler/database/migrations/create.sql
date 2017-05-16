CREATE TABLE `douban` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`mid` INT(11) NOT NULL COMMENT '豆瓣电影id',
	`summary` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`name` CHAR(50) NOT NULL COMMENT '电影名' COLLATE 'utf8_unicode_ci',
	`images` VARCHAR(500) NOT NULL COLLATE 'utf8_unicode_ci',
	`director` CHAR(50) NOT NULL COMMENT '导演' COLLATE 'utf8_unicode_ci',
	`actor` VARCHAR(1000) NOT NULL COMMENT '主演' COLLATE 'utf8_unicode_ci',
	`type` VARCHAR(100) NOT NULL COMMENT '类型' COLLATE 'utf8_unicode_ci',
	`region` CHAR(50) NOT NULL COMMENT '产地' COLLATE 'utf8_unicode_ci',
	`publishdate` CHAR(100) NOT NULL COMMENT '发布日期' COLLATE 'utf8_unicode_ci',
	`avgrating` CHAR(5) NOT NULL COMMENT '平均评分' COLLATE 'utf8_unicode_ci',
	`commentcount` INT(11) NOT NULL COMMENT '评论数',
	`ratingcount` INT(11) NOT NULL COMMENT '打分人数',
	`addtime` DATETIME NOT NULL COMMENT '添加时间',
	`updatetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COMMENT='豆瓣'
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM
AUTO_INCREMENT=1
;
