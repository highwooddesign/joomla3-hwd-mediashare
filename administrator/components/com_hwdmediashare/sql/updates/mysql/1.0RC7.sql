-- $Id: 1.0RC7.sql 408 2012-06-08 17:17:43Z dhorsfall $

CREATE TABLE IF NOT EXISTS `#__hwdms_mailq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` text NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `email_type` int(11) unsigned NOT NULL DEFAULT '0',
  `attempts` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` varchar(5120) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id_alias` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;
