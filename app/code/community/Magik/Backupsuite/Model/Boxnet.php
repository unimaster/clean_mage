<?php

class Magik_Backupsuite_Model_Boxnet extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('backupsuite/boxnet');
    }  
   
    public function uploadFileToBoxnet($fname,$getProfileId)
    {   
	Zend_Loader::loadClass('BoxAPI');	
	// $client_id		= 'oyu85q30qasmeri1qxuo7sw3w81ozyve';
	// $client_secret 	= 'dnx6QHRwnL96cfk8NL4uHtqyQJP6EhoG';
	$client_id		= Mage::getStoreConfig('settings/boxnetappkey');
	$client_secret 	= Mage::getStoreConfig('settings/boxnetsecret');
	$redirect_uri 	= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
	$box = new BoxAPI($client_id, $client_secret, $redirect_uri);
	if(!$box->load_token($getProfileId)){
		if(isset($_GET['code'])){
			$token = $box->get_token($_GET['code'], true);
			if($box->write_token($token, 'file',$getProfileId)){
				$box->load_token($getProfileId);
			}
		} else {
			$box->get_code();
		}
	}

	$tokenDetails=$box->read_token('','',$getProfileId);

	
	// Get folder details
	$folderExists=$box->get_folder_details('0');
	
	$backupFolderDeatails=$this->search_array($folderExists['item_collection']['entries'], 'name', 'MagikBackup');
	$profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($getProfileId);
	
	if($profileData['path']!=''){
		$fpath=$profileData['path']."/".$fname;
	}else{ 
		$fpath=Mage::getBaseDir().DS.'var'.DS.'magikbackup'."/".$fname;
	}
	
	if($folderExists['item_collection']['total_count'] > 0 && isset($backupFolderDeatails[0]['id']))	{
	   
	   //  var_dump(searchNestedArray($folderExists['item_collection']['entries'], 'New Folder 1'));	   
		$box->put_file($fpath, $backupFolderDeatails[0]['id']);	
		//$folderItems=$box->get_folder_items($backupFolderDeatails[0]['id']);
		//print_r($folderItems);
	} else {
		$parent = array();
		$parent['id'] = '0';
		$params = array();
		$params['name'] = 'MagikBackup';
		$params['parent'] = $parent;		
		$params = json_encode($params);		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/folders");
		curl_setopt($ch, CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$tokenDetails['access_token']));
		$result = curl_exec($ch);
		curl_close($ch);
		
		$newFolderDetails=json_decode($result,true);		
		$box->put_file($fpath, $newFolderDetails['id']);	   
		
	}	    	
	return;	

    }

    public function getBoxAccountDetails($get_profileid)
    {
	Zend_Loader::loadClass('BoxAPI');	
	// $client_id		= 'oyu85q30qasmeri1qxuo7sw3w81ozyve';
	// $client_secret 	= 'dnx6QHRwnL96cfk8NL4uHtqyQJP6EhoG';
	$client_id		= Mage::getStoreConfig('settings/boxnetappkey');
	$client_secret 	= Mage::getStoreConfig('settings/boxnetsecret');
	$redirect_uri 	= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
	$box = new BoxAPI($client_id, $client_secret, $redirect_uri);

	if(!$box->load_token($get_profileid)){ 
		if(isset($_GET['code'])){ 
			$token = $box->get_token($_GET['code'], true);
			if($box->write_token($token, 'file', $get_profileid)){
				$box->load_token($get_profileid);
			}
		} else { 
			$box->get_code();
		}
	}
	$userDetails=$box->get_user();
	return $userDetails;
    }


    public function search_array ( $array, $key, $value )
    {
	$results = array();

	if ( is_array($array) )
	{
	    if ( $array[$key] == $value )
	    {
		$results[] = $array;
	    } else {
		foreach ($array as $subarray) 
		    $results = array_merge( $results, $this->search_array($subarray, $key, $value) );
	    }
	}

	return $results;
    }


    public function removeFromBoxnet($file,$getProfileId)
    {
	Zend_Loader::loadClass('BoxAPI');	
	// $client_id		= 'oyu85q30qasmeri1qxuo7sw3w81ozyve';
	// $client_secret 	= 'dnx6QHRwnL96cfk8NL4uHtqyQJP6EhoG';
	$client_id		= Mage::getStoreConfig('settings/boxnetappkey');
	$client_secret 	= Mage::getStoreConfig('settings/boxnetsecret');
	$redirect_uri 	= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
	$box = new BoxAPI($client_id, $client_secret, $redirect_uri);
	if(!$box->load_token($getProfileId)){
		if(isset($_GET['code'])){
			$token = $box->get_token($_GET['code'], true);
			if($box->write_token($token, 'file',$getProfileId)){
				$box->load_token($getProfileId);
			}
		} else {
			$box->get_code();
		}
	}
	
	$tokenDetails=$box->read_token('','',$getProfileId);
	$folderExists=$box->get_folder_details('0');
	
	$backupFolderDeatails=$this->search_array($folderExists['item_collection']['entries'], 'name', 'MagikBackup');

	$filesArray=$box->get_folder_items($backupFolderDeatails[0]['id']);	
	$fileDetails=$this->search_array($filesArray['entries'], 'name', $file);	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.box.com/2.0/files/".$fileDetails[0]['id']);
	curl_setopt($ch, CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");		
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$tokenDetails['access_token'],"If-Match: ".$fileDetails[0]['etag']));
	$result = curl_exec($ch);
	curl_close($ch);
	return;


    }
	 
}
