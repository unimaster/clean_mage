<?php
class Magik_Backupsuite_Model_Config_Dbstructure {
    
  public function toOptionArray() { 
	  
      $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
      $sql = "SELECT TABLE_NAME,round(((data_length + index_length) / 1024 / 1024 ), 2) Size FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname'";
      $collection = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);

      $tableList = array();
	   $tableList[]=array('label'=> 'Table Name  [ Disk Space in MB]','value' => ''); 
	  foreach ($collection as $db) {
	   
            $tableList[] = ( array(
                'label' => (string) $db['TABLE_NAME'].' [ '.$db['Size'].' mb]',
                'value' => $db['TABLE_NAME']
                    ));
	    }
	  return $tableList;
   }

}