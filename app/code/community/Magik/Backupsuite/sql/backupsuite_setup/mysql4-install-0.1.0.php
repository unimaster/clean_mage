<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('backupsuite')};
CREATE TABLE {$this->getTable('backupsuite')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime default NULL,
  `type` int(3) Default 0,
  `filename` text NOT NULL,
  `file_id` varchar(255) default NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
");
$installer->endSetup(); 
