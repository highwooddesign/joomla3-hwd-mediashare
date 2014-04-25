#
# Database updates for 1.1.1
#

ALTER TABLE `#__hwdms_config` ADD COLUMN `id` int(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id);