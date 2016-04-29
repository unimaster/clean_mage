<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'dbfile_id', 'varchar(255) NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'dbfilename', 'text NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'storage_type', 'smallint(5)');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'profile_id', 'int(15)');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'name', 'varchar(255) NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'description', 'text NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'log_detail', 'text NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('backupsuite'),'by_cron', 'smallint(5)');
$installer->getConnection()->addColumn($installer->getTable('cron_schedule'),'profile_id', 'int(15)') ;
$installer->run("DROP TABLE IF EXISTS {$this->getTable('backupsuiteprofile')};
CREATE TABLE {$this->getTable('backupsuiteprofile')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `free_disk_space` varchar(20) NOT NULL,
  `backup_error_delete_local` varchar(10) NOT NULL,
  `backup_error_delete_cloud` varchar(10) NOT NULL,
  `disable_cache` varchar(10) NOT NULL,
  `defaultprofile` smallint(6),
  `type` smallint(6),
  `path` text,
  `storage_type` varchar(15) NOT NULL,
  `filesexclusion` text NOT NULL,
  `dbexclusion` text NOT NULL,
  `cron_enable` smallint(6),
  `cron_type` varchar(10),
  `cron_time_frequency` varchar(10) NOT NULL,
  `cron_time_hour` varchar(10) NOT NULL,
  `cron_time_minutes` varchar(10) NOT NULL,
  `cron_time_expression` varchar(50) NOT NULL,
  `cron_expr` varchar(255) NOT NULL,
  `success_recipient` text NOT NULL,
  `success_sender` text NOT NULL,
  `success_template` text NOT NULL,
  `delete_enable` smallint(5),
  `max_backups` varchar(10) NOT NULL,
  `delete_days` varchar(10) NOT NULL,
  `delete_success_recipient` text NOT NULL,
  `delete_success_sender` text NOT NULL,
  `delete_success_template` text NOT NULL,
  `delete_error_recipient` text NOT NULL,
  `delete_error_sender` text NOT NULL,
  `delete_error_template` text NOT NULL,
  `db_host` varchar(255) NOT NULL,
  `db_username` varchar(255) NOT NULL,
  `db_password` varchar(255) NOT NULL,
  `db_name` varchar(255) NOT NULL,
  `email_storage` smallint(6),
  `email_storage_address` text NOT NULL,
  `ftp_storage` smallint(6),
  `ftp_server` varchar(255) NOT NULL,
  `ftp_username` varchar(255) NOT NULL,
  `ftp_password` varchar(255) NOT NULL,
  `amazon_storage` smallint(6),
  `amazon_access_key` text NOT NULL,
  `amazon_secret_key` text NOT NULL,
  `amazon_bucket` text NOT NULL,
  `dropbox_storage` smallint(6),
  `box_storage` smallint(6),
  `gdrive_storage` smallint(6),
  `gdrive_authcode` text NOT NULL,
  `logs_path` text NOT NULL,
  `logs_level` varchar(15),
  `auth_enable` smallint(6),
  `auth_user` varchar(255) NOT NULL,
  `auth_pass` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


");
$installer->endSetup(); 