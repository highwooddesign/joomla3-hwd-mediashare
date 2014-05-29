#
# Database updates for 2.0.0RC2
#

CREATE TABLE IF NOT EXISTS `#__hwdms_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actor` int(11) unsigned NOT NULL,
  `action` int(11) unsigned NOT NULL,
  `target` int(11) unsigned NOT NULL,
  `verb` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `actor` (`actor`),
  KEY `action` (`action`),
  KEY `target` (`target`),
  KEY `verb` (`verb`),
  KEY `created` (`created`),
  KEY `access` (`access`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;