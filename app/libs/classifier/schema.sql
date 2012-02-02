CREATE TABLE IF NOT EXISTS `log_classifiers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `value` varchar(255) NOT NULL,
  `ham_count` bigint(20) NOT NULL,
  `spam_count` bigint(20) NOT NULL,
  `spamicity` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
