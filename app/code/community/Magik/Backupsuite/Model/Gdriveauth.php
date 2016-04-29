<?php
class Magik_Backupsuite_Model_Gdriveauth extends Mage_Core_Model_Config_Data
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('backupsuite/gdriveauth');
    }

    public function uploadToGdrive($upfile,$get_profileid)
    {
	 require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Client.php');
	 require_once(Mage::getBaseDir('lib') . '/googledrive/Google_DriveService.php');
	 require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Oauth2Service.php');
	  
	  // initialise the client
	 $client = new Google_Client();
	 $oauth2 = new Google_Oauth2Service($client);
	
	 $client->setClientId(Mage::getStoreConfig('settings/gdriveappkey'));
	 $client->setClientSecret(Mage::getStoreConfig('settings/gdrivesecret'));
	 $client->setRedirectUri(Mage::getStoreConfig('settings/gdriveredirect'));
	 $client->setScopes(array('https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/userinfo.profile'));

	 $service = new Google_DriveService($client);
		  
	 $client->setAccessToken($this->authenticate($client,$get_profileid));    
      
	 // initialise the Google Drive service
	$service = new Google_DriveService($client);	
	$profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($get_profileid);
	
	if($profileData['path']!=''){
		$pdfFile=$profileData['path']."/".$upfile;
	}else{ 
		$pdfFile = Mage::getBaseDir().DS.'var'.DS.'magikbackup'."/".$upfile;
	}
	
	// create and upload a new Google Drive file, including the data
	try {
		$file = new Google_DriveFile();
		$file->setTitle(basename($pdfFile));
		$file->setMimeType('application/octet-stream');
		
		$result = $service->files->insert($file, array('data' => file_get_contents($pdfFile), 'mimeType' => 'application/octet-stream'));
	}
	catch (Exception $e) {
		print $e->getMessage();
	}
	//echo "<prE>";
	//print_r($result); 
	return $result['id'];
    }  

    public function authenticate($client,$get_profileid) {
	    $file = Mage::getBaseDir().DS.'var'.DS.'data'.'/token_'.$get_profileid.'.json';
		
		if (file_exists($file)) return file_get_contents($file);
		
		//$_GET['code'] = ''; // insert the verification code here
	 
		// print the authentication URL
		if ($authCode=='') {
			header("Location:".$client->createAuthUrl(array('https://www.googleapis.com/auth/drive.file')));
			exit;
		}
	 
		//file_put_contents($file, $client->authenticate($authCode));	exit;    
		//exit('Authentication saved to token.json - now run this script again.');
    }

    public function removeFromGdrive($fileId,$dbfileid,$get_profileid)
    {	
      require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Client.php');
	  require_once(Mage::getBaseDir('lib') . '/googledrive/Google_DriveService.php');
	  require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Oauth2Service.php');
	  
	  // initialise the client
		 $client = new Google_Client();
		 $oauth2 = new Google_Oauth2Service($client);
		  
		 $client->setClientId(Mage::getStoreConfig('settings/gdriveappkey'));
	 	 $client->setClientSecret(Mage::getStoreConfig('settings/gdrivesecret'));
	 	 $client->setRedirectUri(Mage::getStoreConfig('settings/gdriveredirect'));
		 $client->setScopes(array('https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/userinfo.profile'));

		 $client->setAccessToken($this->authenticate($client,$get_profileid));  
		 
		 $service = new Google_DriveService($client);	
    	try {
		    $service->files->delete($fileId);
		    $service->files->delete($dbfileid);
		} catch (Exception $e) {
		    print "An error occurred: " . $e->getMessage();
		}

    }

    function printAbout($get_profileid) {
      require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Client.php');
	  require_once(Mage::getBaseDir('lib') . '/googledrive/Google_DriveService.php');
	  require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Oauth2Service.php');
	  
	  // initialise the client
		 $client = new Google_Client();
		 $oauth2 = new Google_Oauth2Service($client);
		 
		 $client->setClientId(Mage::getStoreConfig('settings/gdriveappkey'));
		 $client->setClientSecret(Mage::getStoreConfig('settings/gdrivesecret'));
		 $client->setRedirectUri(Mage::getStoreConfig('settings/gdriveredirect'));
		 $client->setScopes(array('https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/userinfo.profile'));

		 
		 $client->setAccessToken($this->authenticate($client,$get_profileid));   
		 $service = new Google_DriveService($client);		

	  try {
	    if($client->getAccessToken()){ 
      	    $user = $oauth2->userinfo->get();
      	    
      	    return $user['name'];      
      	}
	  } catch (Exception $e) {
	    print "An error occurred: " . $e->getMessage();
	}
}

	
} 

?>
