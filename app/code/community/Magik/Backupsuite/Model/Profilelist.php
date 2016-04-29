<?php
class Magik_Backupsuite_Model_Profilelist
{
    public function toOptionArray()
      {   
             
	  $collection = Mage::getModel('backupsuite/backupsuiteprofile')->getCollection();
	  $collection->load();
	  $counter_profile=count($collection);
	  $profilelist = array();

	  if($counter_profile > 0){
	      $profilelist[] = array(
		    'label' => Mage::helper('backupsuite')->__('-- Please select a Profile -- '),
		    'value' => ''
		);
	      foreach ($collection as $profile) {
		      $profilelist[] = ( array(
					      'label' => (string) $profile->getName(),
					      'value' => $profile->getId()
				    ));
	      }  
	      return $profilelist;	
	  }else{
		$profilelist[] = array(
		    'label' => Mage::helper('backupsuite')->__('-- Not Any Profile Added--'),
		    'value' => ''
		);
		return $profilelist;	
	  }
      }

}