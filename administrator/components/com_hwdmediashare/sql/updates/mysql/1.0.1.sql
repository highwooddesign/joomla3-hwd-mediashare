#
# Database updates for 1.0.1
#

ALTER TABLE `#__hwdms_media` ADD COLUMN `viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `location`;