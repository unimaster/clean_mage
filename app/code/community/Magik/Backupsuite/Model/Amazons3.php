<?php

class Magik_Backupsuite_Model_Amazons3 extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('backupsuite/amazons3');
    }

    public function calls3($db_backup_file, $path, $access_key, $secret_key, $bucket)
    {
			Zend_Loader::loadClass('S3');		
      try{
			    $s3 = new S3($access_key, $secret_key);
			    //$s3->putBucket($bucket, S3::ACL_PRIVATE);	    
			    //flush();
			    $s3->putObjectFile($path, $bucket, $db_backup_file, S3::ACL_PRIVATE);	     
			    //echo "Backup Complete";
			}
			catch(Exception $e){
	    	echo $e->getMessage();	    
			}
    }

    public function removeS3($path,$bucket,$access_key,$secret_key)	 	
    { 
			Zend_Loader::loadClass('S3');
			$s3 = new S3($access_key, $secret_key);
			$s3->deleteObject($bucket,$path);
    }
	 
}
