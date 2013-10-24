-- $Id: install.mysql.utf8.sql 425 2012-06-28 07:48:57Z dhorsfall $

CREATE TABLE IF NOT EXISTS `#__hwdms_migrator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `migration_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;