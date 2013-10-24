-- $Id: 1.1.1.sql 924 2013-01-16 11:14:38Z dhorsfall $

#
# Database updates for 1.1.0 to 1.1.1
#

ALTER TABLE `#__hwdms_config` ADD COLUMN `id` int(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id);