-- $Id: install.mysql.utf8.sql 922 2013-01-16 11:11:32Z dhorsfall $

CREATE TABLE IF NOT EXISTS `#__hwdms_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `reply_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `likes` int(11) unsigned NOT NULL DEFAULT '0',
  `dislikes` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
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
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `thumbnail_ext_id` int(11) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `private` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `likes` int(11) unsigned NOT NULL DEFAULT '0',
  `dislikes` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
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
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_album_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) unsigned NOT NULL DEFAULT '0',
  `album_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_category_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_content_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) unsigned NOT NULL DEFAULT '0',
  `content_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_ext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext` varchar(10) NOT NULL DEFAULT '',
  `media_type` int(11) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` varchar(5120) NOT NULL,
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_ext` (`ext`),
  KEY `idx_media` (`media_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_favourites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `min` int(5) unsigned NOT NULL,
  `max` int(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `tooltip` text NOT NULL,
  `visible` tinyint(3) unsigned DEFAULT '0',
  `required` tinyint(3) unsigned DEFAULT '0',
  `searchable` tinyint(3) unsigned DEFAULT '1',
  `options` text,
  `fieldcode` varchar(255) NOT NULL,
  `params` varchar(5120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fieldcode` (`fieldcode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_fields_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `field_id` int(11) unsigned NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  `access` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `user_id` (`element_type`),
  KEY `idx_user_fieldid` (`element_type`,`field_id`),
  KEY `access` (`access`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `file_type` int(11) unsigned NOT NULL DEFAULT '0',
  `basename` varchar(32) NOT NULL DEFAULT '',
  `ext` varchar(10) NOT NULL DEFAULT '',
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  `checked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `download` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` varchar(5120) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id_alias` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `thumbnail_ext_id` int(11) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `private` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `likes` int(11) unsigned NOT NULL DEFAULT '0',
  `dislikes` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
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
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_group_invite` (
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hwdms_group_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_group_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0',
  `approved` tinyint(3) NOT NULL DEFAULT '0',
  `permissions` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `idx_id` (`id`),
  KEY `idx_groupid` (`group_id`),
  KEY `idx_memberid` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `like` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28;

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

CREATE TABLE IF NOT EXISTS `#__hwdms_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ext_id` int(11) unsigned NOT NULL DEFAULT '0',
  `media_type` int(11) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `type` int(11) unsigned NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL,
  `storage` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `streamer` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `embed_code` mediumtext NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `thumbnail_ext_id` int(11) unsigned NOT NULL DEFAULT '0',
  `location` varchar(255) NOT NULL,
  `viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `private` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `likes` int(11) unsigned NOT NULL DEFAULT '0',
  `dislikes` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `download` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` varchar(5120) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id_alias` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`status`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_media_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id_1` int(11) unsigned NOT NULL DEFAULT '0',
  `media_id_2` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `thumbnail_ext_id` int(11) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `private` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `likes` int(11) unsigned NOT NULL DEFAULT '0',
  `dislikes` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
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
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_playlist_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL DEFAULT '0',
  `media_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_type` int(11) unsigned NOT NULL DEFAULT '0',
  `media_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `attempts` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` varchar(5120) NOT NULL,
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_process_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) unsigned NOT NULL DEFAULT '0',
  `input` varchar(5120) NOT NULL DEFAULT '',
  `output` varchar(5120) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `params` varchar(5120) NOT NULL,
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `report_id` int(11) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_response_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) unsigned NOT NULL DEFAULT '0',
  `response_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tag` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_tag_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_type` int(11) unsigned NOT NULL DEFAULT '0',
  `element_id` int(11) unsigned NOT NULL DEFAULT '0',
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `#__hwdms_upload_tokens` (
  `userid` int(11) NOT NULL,
  `token` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hwdms_users` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `thumbnail_ext_id` int(11) unsigned NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5120) NOT NULL DEFAULT '',
  `private` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `likes` int(11) unsigned NOT NULL DEFAULT '0',
  `dislikes` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `featured` tinyint(3) NOT NULL DEFAULT '0',
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
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__hwdms_config` (`id`, `name`, `date`, `params`) VALUES
(1, 'config', '0000-00-00 00:00:00', '{}');

INSERT INTO `#__hwdms_ext` (`id`, `ext`, `media_type`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `created_user_id`, `created`, `publish_up`, `publish_down`, `modified_user_id`, `modified`) VALUES
(28, 'jpeg', 3, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(29, 'jpg', 3, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(30, 'mov', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(31, 'mpeg', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(32, 'png', 3, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(33, 'divx', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(34, 'pdf', 2, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(35, 'flv', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(36, 'wma', 1, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(37, 'mp3', 1, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(38, 'gif', 3, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(39, 'mpg', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(40, 'avi', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(41, 'mp4', 4, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00'),
(42, 'aac', 1, 1, 0, '0000-00-00 00:00:00', 1, '', 42, '2012-01-25 12:00:00', '2012-01-25 12:00:00', '0000-00-00 00:00:00', 42, '0000-00-00 00:00:00');