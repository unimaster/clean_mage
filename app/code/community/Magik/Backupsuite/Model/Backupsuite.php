<?php

class Magik_Backupsuite_Model_Backupsuite extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("backupsuite/backupsuite");

    }

    public function markArchive($dt, $type, $filename, $dbfilename, $fileId, $dbfileId,$storageType,$get_profileId,$changelog)
    {	
	
	$currentdate=date("M d,Y h:i:s a", Mage::getModel('core/date')->timestamp(time()));
	$bkname="Automatic Backup ".$currentdate;

    	$insertArchive = Mage::getModel('backupsuite/backupsuite');
	    $insertArchive->setCreated($dt);
	    $insertArchive->setType($type);
	    $insertArchive->setFilename($filename);
	    $insertArchive->setFileId($fileId);
	    $insertArchive->setDbfileId($dbfileId);
	    $insertArchive->setDbfilename($dbfilename);
	    $insertArchive->setStorageType($storageType);
	    $insertArchive->setProfileId($get_profileId);
	    $insertArchive->setName($bkname);
	    $insertArchive->setLogDetail($changelog);
	    $insertArchive->setByCron(1);
            $insertArchive->save();

	   
    }

    public function removefromDb($id)		
    {
        $blogpost = Mage::getModel('backupsuite/backupsuite');
        $blogpost->load($id);
        $blogpost->delete();
    }	

    public function getAllArchives($day,$get_profileId)
    {
    	$date_model = Mage::getModel('core/date');
    	$todayDate = $date_model->date('Y-m-d H:i:s');
    	$previous_date = date('Y-m-d H:i:s', strtotime("$todayDate -$day days"));

    	$archive_data = Mage::getModel('backupsuite/backupsuite')
    				 ->getCollection()
				->addFieldToFilter('profile_id', array('eq' => $get_profileId))
    				 ->addFieldToFilter('created', array('lt'=>$previous_date));
	return $archive_data;
    }
    public function getMaxArchives($maxbackup,$get_profileId)
    {
	/* Get Collection Count */
    	$countBackup=Mage::getModel('backupsuite/backupsuite')->getCollection();
	$Maxcount=$countBackup->getSize();

	if($Maxcount>$maxbackup){
	    $requireBackup=$Maxcount-$maxbackup;
	    $archive_data = Mage::getModel('backupsuite/backupsuite')->getCollection(); 
	    $archive_data->addFieldToFilter('profile_id', array('eq' => $get_profileId));
	    $archive_data->getSelect()->order('created ASC')->limit($requireBackup);
	    return $archive_data;
	}
    	
    }
    
    
}
	 