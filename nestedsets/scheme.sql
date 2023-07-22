CREATE TABLE `menu_entry`(
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Наименование узла для человека',
	`name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Имя узла для URL',
	`left_index` INTEGER UNSIGNED NOT NULL DEFAULT '0',
	`right_index` INTEGER UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY(`id`),
	KEY `idx_li`(`left_index`),
	KEY `idx_ri`(`right_index`)
) ENGINE=InnoDB;