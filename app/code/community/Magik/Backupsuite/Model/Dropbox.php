<?php

class Magik_Backupsuite_Model_Dropbox extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('backupsuite/dropbox');
    }   

    public function getAccountDetails($get_profileid)
    {	 
 		Zend_Loader::loadClass('DropboxClient');
      
        $dropbox = new DropboxClient(array(
          'app_key' => Mage::getStoreConfig('settings/dropboxappkey'), 
          'app_secret' => Mage::getStoreConfig('settings/dropboxappsecret'),
          'app_full_access' => false,
        ),'en');
	$tokenname='access_'.$get_profileid;
        $access_token = $this->load_token($tokenname);
		if(!empty($access_token)) {
			$dropbox->SetAccessToken($access_token);			
			//print_r($access_token);
		}
    	return $dropbox->GetAccountInfo();

    }

   
    public function store_token($token, $name)
    {
      $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
      if(!is_writable($dirPath)) {
          $file = new Varien_Io_File();
          $dataDir = $file->mkdir($dirPath);
      }
      if(!file_put_contents($dirPath."/$name.token", serialize($token)))
        die('<br />Could not store token! <b>Make sure that the directory `data` exists and is writable!</b>');
    }

    public function load_token($name)
    {
      $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
      if(!file_exists($dirPath."/$name.token")) return null;
      return @unserialize(@file_get_contents($dirPath."/$name.token"));
    }

    public function delete_token($name)
    {
      $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
      @unlink($dirPath."/$name.token");
    }

    public function uploadToDropbox($filename,$get_Profileid)
    {

    	Zend_Loader::loadClass('DropboxClient');
      
        $dropbox = new DropboxClient(array(
          'app_key' => Mage::getStoreConfig('settings/dropboxappkey'), 
          'app_secret' => Mage::getStoreConfig('settings/dropboxappsecret'),
          'app_full_access' => false,
        ),'en');
	$tokenname='access_'.$get_Profileid;
        $access_token = $this->load_token($tokenname);
		if(!empty($access_token)) {
			$dropbox->SetAccessToken($access_token);			
		}

	$profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($get_Profileid);
	
	if($profileData['path']!=''){
		$path=$profileData['path'];
	}else{ 
		$path=Mage::getBaseDir().DS.'var'.DS.'magikbackup';
	}
		$src=$path."/".$filename;
    	if ( file_exists($src) )
        {
	        try {
	            $upload_name =$src;
	            $result='';
	            //$result="<pre>";
	            //$result.= "\r\n\r\n<b>Uploading ".$upload_name.":</b>\r\n";
	            $meta = $dropbox->UploadFile($src);  //upload it!
	            //$result.=print_r($meta,true);
	            //$result.= "\r\n done!";
	            //$result.="</pre>";
	 
	        	$result.= '<span style="color: green">File successfully uploaded to your Dropbox!</span>';
	        } catch(Exception $e) {
	        	$result.='<span style="color: red">Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
	        }
	        //echo $result;
        }

    }
	 

 function removeFromDropbox($filename,$get_profileid)
    {

    	Zend_Loader::loadClass('DropboxClient');
      
        $dropbox = new DropboxClient(array(
          'app_key' => Mage::getStoreConfig('settings/dropboxappkey'), 
          'app_secret' => Mage::getStoreConfig('settings/dropboxappsecret'),
          'app_full_access' => false,
        ),'en');
	$tokenname='access_'.$get_profileid;
        $access_token = $this->load_token($tokenname);
		if(!empty($access_token)) {
			$dropbox->SetAccessToken($access_token);			
		}
		$dropbox->delete('/'.$filename);
    }
	 
	    
}
