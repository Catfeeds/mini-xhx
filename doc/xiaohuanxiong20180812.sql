CREATE TABLE `x_share_article_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '文章id',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户openid',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;