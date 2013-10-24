-- $Id: 1.0.1.sql 464 2012-08-14 09:30:24Z dhorsfall $

#
# Database updates for 1.0 to 1.0.1
#

ALTER TABLE `#__hwdms_media` ADD COLUMN `viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `location`;