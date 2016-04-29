<?php
class Magik_Backupsuite_Model_Observer
{
protected $_errors = array();
protected $_delerrors = array();
    public function execbackup($schedule) {
	$this->_errors = array();
	$this->_delerrors = array();
    try { 
	
	//$profile_Id=1;
	$profile_Id=$schedule->getProfileId();
	$profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($profile_Id);
	$checkBackuptype=$profileData['type'];
	$profileStoragetype=$profileData['storage_type'];

	if($profileData['logs_level']=='ALL'){
	    error_reporting(E_ALL);
	}
	if($profileData['logs_level']=='WARNING'){
	    error_reporting(E_ERROR | E_WARNING);
	}
	if($profileData['logs_level']=='OFF'){
	    error_reporting(0);
	}
	/* Log Directory */
	$dirpathLog = Mage::getBaseDir().DS.'var'.DS.'log'.DS.'magikbackuplog';
	if(!is_writable($dirpathLog)) {
		$file = new Varien_Io_File();
		$dataDir = $file->mkdir($dirpathLog);
		$contentStr='Order deny,allow'. "\n";	
		$contentStr.='Allow from all'	;
		file_put_contents($dirpathLog."/.htaccess", $contentStr);
	}
	$changelog='magikbackuplog/backupsuite_'.time().'.log';
	$val=null;

	/* Create Backup folder with htaccess*/
	if($profileData['path']!=''){
		$backupDirPath=$profileData['path'];
	}else{
		$backupDirPath = Mage::getBaseDir().DS.'var'.DS.'magikbackup';
	}

	/* Check Free Space Before Backup */
	$md="df -kh ".$backupDirPath." |awk '{ printf \"%s \" ,$4 }' |cut -d ' ' -f 2";
	$getSpace=exec($md);
	$checkspace = round($profileData['free_disk_space'] / 1024,2);
	$calSpace=substr($getSpace, 0, -1);
	if($calSpace<$checkspace){ 
	      $translate = Mage::getSingleton('core/translate');
	      $translate->setTranslateInline(false);

	      $toemails=explode(",",$profileData['success_recipient']);
	      $templateId = $profileData['success_template'];      
	      $mailSubject = 'No Free space available to backup ';
	      $senderName=$profileData['success_sender'];
	      $senderEmail ='test@domain.com';    
	      $sender = array('name' => $senderName,'email' => $senderEmail);
	      
	      $storeId = Mage::app()->getStore()->getId();
	      $vars=array('warnings' => 'Check your disk space for backup');
	      $recepientName = 'John Doe';   
	      foreach($toemails as $toemail){ 
		$translate  = Mage::getSingleton('core/translate');
	      // Send Transactional Email
	      Mage::getModel('core/email_template')
	      ->setTemplateSubject($mailSubject)
	      ->sendTransactional($templateId, $sender, $toemail, null, $vars, $storeId);
		$translate->setTranslateInline(true); 
	    }
	    return false;
	}

	if(!is_writable($backupDirPath)) {
		$file = new Varien_Io_File();
		$dataDir = $file->mkdir($backupDirPath);
		$contentStr='Order deny,allow'. "\n";	
		$contentStr.='Allow from all'	;
		file_put_contents($backupDirPath."/.htaccess", $contentStr);
	}

	/* Disable cache during backup start */
	if($profileData['disable_cache']==1){
		$checkcache=1;
		Mage::app()->getCacheInstance()->flush();
		$allTypes = Mage::app()->useCache();
		$types=array('config','layout','block_html','translate','collections','eav','config_api','config_api2');
		$updatedTypes = 0;
		foreach ($types as $code) { 
			
		    if (!empty($allTypes[$code])) { 
			      $allTypes[$code]=0; 
			      $updatedTypes++;
			      $checkcache=2;
			  }
		    $tags = Mage::app()->getCacheInstance()->cleanType($code);
		}
		if ($updatedTypes > 0) {
			Mage::app()->saveUseCache($allTypes);
		}     
	}

	/* Create Database backup */
	if($checkBackuptype==0){
		/* Tables Exclusion*/
		$IgnoretableList='';
		$TablesIgnoreList=$profileData['dbexclusion'];
		$AllIgnoretables=explode(',',$TablesIgnoreList);
		$ignoretable='--ignore-table='.$profileData['db_name'].'.';
		  foreach($AllIgnoretables as $All_Ignoretables){
		    $IgnoretableList.=$ignoretable.$All_Ignoretables.' ';
		  }

		  if($profileData['suffix']!=''){   $db_file = $profileData['db_name'].'-'.time().'_'.$profileData['suffix'].'.sql.gz'; }else{
                         $db_file = $profileData['db_name'].'-'.time().'.sql.gz';
		  }
		Mage::log('Backup process Started', $val,$changelog);
		$logDirPath = Mage::getBaseDir().DS.'var'.DS.'log'.DS.$changelog;
		$cmd = "mysqldump -alv -h ".$profileData['db_host']." -u".$profileData['db_username']." -p".$profileData['db_password']."  ".$profileData['db_name']." ".$IgnoretableList."2>>". $logDirPath." | gzip > ".$backupDirPath."/".$db_file;
		 system($cmd);

		 /* Files Exclusion */
		 $IgnorePathList=array();	
		 $BackupIgnorePaths=$profileData['filesexclusion'];
		 $AllIgnorePaths=explode(',',$BackupIgnorePaths);
		 foreach($AllIgnorePaths as $All_IgnorePaths){
			array_push($IgnorePathList,Mage::getBaseDir() . DS.$All_IgnorePaths);
		 }
		Mage::log('Finish database backup', $val,$changelog);
		Mage::log('Start filesystem backup', $val,$changelog);
		 $type="filesystem";
		 $fileExt='tgz';
		 $tm=time();
		 
		$backupManager = Mage_Backup::getBackupInstance($type)
				  ->setBackupExtension($fileExt)
				  ->setTime($tm)
				  ->setBackupsDir($backupDirPath);
		if($profileData['suffix']!=''){ 
			$backupManager->setName($profileData['suffix']);
		}
		  if($profileData['suffix']!=''){   
							 $backup_filename=$tm."_".$type.'_'.$backupManager->getName().".".$fileExt; 
						      }else{
							  $backup_filename=$tm."_".$type.".".$fileExt;
						      }	
		Mage::register('backup_manager', $backupManager);
		if($BackupIgnorePaths!=''){ 
		      $backupManager->setRootDir(Mage::getBaseDir())
				    ->addIgnorePaths($IgnorePathList);
		}else{  $backupManager->setRootDir(Mage::getBaseDir()); }
		$backupManager->create();
		Mage::log('End filesystem backup', $val,$changelog);
		/* Google Drive Storage */			      
		if($profileData['storage_type']==5) {
			$filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.json';
			if(is_file($filePath)) { 
						  $dbID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($db_file,$profile_Id);
						  $fileID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($backup_filename,$profile_Id);
			}
		 
		}
		Mage::log( 'Start packing files', $val,$changelog);
		Mage::getModel('backupsuite/backupsuite')->markArchive(now(), 0,$backup_filename,$db_file,$fileID,$dbID,$profileStoragetype,$profile_Id,$changelog);
		Mage::log( 'Finish packing files', $val,$changelog);
		Mage::log( 'Finish directories and files backup', $val,$changelog);
		Mage::log( 'Backup was successfully saved', $val,$changelog);
		Mage::log( 'Backup process finished', $val,$changelog);
	}
	     
	if($checkBackuptype==1){
		/* Files Exclusion */
		$IgnorePathList=array();	
		$BackupIgnorePaths=$profileData['filesexclusion'];
		$AllIgnorePaths=explode(',',$BackupIgnorePaths);
		foreach($AllIgnorePaths as $All_IgnorePaths){
			array_push($IgnorePathList,Mage::getBaseDir() . DS.$All_IgnorePaths);
		}
		Mage::log('Start filesystem backup', $val,$changelog);				      					      			      
		$type="filesystem";
		$fileExt='tgz';				      
		$tm=time();				     
		
		$backupManager = Mage_Backup::getBackupInstance($type)
				->setBackupExtension($fileExt)
				->setTime($tm)
				->setBackupsDir($backupDirPath);
		if($profileData['suffix']!=''){ 
			$backupManager->setName($profileData['suffix']);
		}
		if($profileData['suffix']!=''){   
							 $backup_filename=$tm."_".$type.'_'.$backupManager->getName().".".$fileExt; 
						      }else{
							  $backup_filename=$tm."_".$type.".".$fileExt;
						      }	
		Mage::register('backup_manager', $backupManager);
		if($BackupIgnorePaths!=''){
			$backupManager->setRootDir(Mage::getBaseDir())
				      ->addIgnorePaths($IgnorePathList);
		}else{  $backupManager->setRootDir(Mage::getBaseDir()); }
		$backupManager->create();
		Mage::log('End filesystem backup', $val,$changelog);				       
		if($profileStoragetype==5) {
			$filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.json';
			if(is_file($filePath)) { 
				$fileID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($backup_filename,$profile_Id);
			}
		}
		Mage::log( 'Start packing files', $val,$changelog);
		Mage::getModel('backupsuite/backupsuite')->markArchive(now(), 0,$backup_filename,'',$fileID,'',$profileStoragetype,$profile_Id,$changelog);
		Mage::log( 'Finish packing files', $val,$changelog);
		Mage::log( 'Finish directories and files backup', $val,$changelog);
		Mage::log( 'Backup was successfully saved', $val,$changelog);
		Mage::log( 'Backup process finished', $val,$changelog);
	}
	if($checkBackuptype==2){

		$IgnoretableList='';
		$TablesIgnoreList=$profileData['dbexclusion'];				    
		$AllIgnoretables=explode(',',$TablesIgnoreList);				    
		$ignoretable='--ignore-table='.$profileData['db_name'].'.';				    
		foreach($AllIgnoretables as $All_Ignoretables){
			$IgnoretableList.=$ignoretable.$All_Ignoretables.' ';
		}				    
		if($profileData['suffix']!=''){   $db_file = $profileData['db_name'].'-'.time().'_'.$profileData['suffix'].'.sql.gz'; 
		}else{ $db_file = $profileData['db_name'].'-'.time().'.sql.gz';}

		Mage::log('Backup process Started', $val,$changelog);
		$logDirPath = Mage::getBaseDir().DS.'var'.DS.'log'.DS.$changelog;

		$cmd = "mysqldump -alv -h ".$profileData['db_host']." -u".$profileData['db_username']." -p".$profileData['db_password']."  ".$profileData['db_name']." ".$IgnoretableList."2>>". $logDirPath." | gzip > ".$backupDirPath."/".$db_file;
		system($cmd);
		Mage::log('Finish getting files for archive', $val,$changelog);
		if($profileStoragetype==5) {
			$filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.json';
			if(is_file($filePath)) { 
				$dbID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($db_file,$profile_Id);
			}
		}
		Mage::log( 'Start packing files', $val,$changelog);
		Mage::getModel('backupsuite/backupsuite')->markArchive(now(), 0,'',$db_file,'',$dbID,$profileStoragetype,$profile_Id,$changelog);
		Mage::log( 'Finish packing files', $val,$changelog);
		Mage::log( 'Finish directories and files backup', $val,$changelog);
		Mage::log( 'Backup was successfully saved', $val,$changelog);
		Mage::log( 'Backup process finished', $val,$changelog);

	}	
	
	if($profileStoragetype==1) {
		$ftp_server_name = $profileData['ftp_server'];//Write in the format "ftp.servername.com"	
		$conn_id = ftp_connect ( $ftp_server_name );// make a connection to the ftp server				    
		$ftp_user_name = $profileData['ftp_username'];					  
		$ftp_user_pass = $profileData['ftp_password'];					  
		$login_result = ftp_login ( $conn_id , $ftp_user_name , $ftp_user_pass );// login with username and password
		$filename = $db_file;
		$storefilename = $backup_filename;					  
		$fileatt = $backupDirPath."/".$filename;
		$storefileatt = $backupDirPath."/".$storefilename;					  
		// check connection					  
		if ((! $conn_id ) || (! $login_result )) {
			Mage::log( "FTP connection has failed!" , $val,$changelog);
			$ftplogdetails="Attempted to connect to $ftp_server_name for user $ftp_user_name" ;
			Mage::log( $ftplogdetails, $val,$changelog);
									  
		}else{ 
			  $ftplogdetails= "Connected to $ftp_server_name, for user $ftp_user_name";
			  Mage::log( $ftplogdetails, $val,$changelog);
		}					  
							  
		if($checkBackuptype==0){
			//files system
			$storefile = $storefileatt;					  
			$fp = fopen($storefile, 'r') or die ("couldn't open!");					 
			$storedestination_file = $storefilename;
			$storesource_file = $storefileatt;
			$upload = ftp_put ( $conn_id , $storedestination_file , $storesource_file , FTP_BINARY );				
			//files system
			$file = $fileatt;
			$fp = fopen($file, 'r') or die ("couldn't open!");					  
			$destination_file = $filename;					  
			$source_file = $fileatt;					  
			$upload = ftp_put ( $conn_id , $destination_file , $source_file , FTP_BINARY );					  				
			ftp_close ( $conn_id );	
			Mage::log( "Files & Database backup successfully done", $val,$changelog);				   
		}
		if($checkBackuptype==1){
			$storefile = $storefileatt;					  
			$fp = fopen($storefile, 'r') or die ("couldn't open!");					  
			$storedestination_file = $storefilename;					  
			$storesource_file = $storefileatt;					  
			$upload = ftp_put ( $conn_id , $storedestination_file , $storesource_file , FTP_BINARY );					  				
			ftp_close ( $conn_id );	
			Mage::log( "Files backup successfully done", $val,$changelog);				   
		}					
		if($checkBackuptype==2){
			$file = $fileatt;					  
			$fp = fopen($file, 'r') or die ("couldn't open!");					  
			$destination_file = $filename;					  
			$source_file = $fileatt;					  
			$upload = ftp_put ( $conn_id , $destination_file , $source_file , FTP_BINARY );					  				
			ftp_close ( $conn_id ); 
			Mage::log( "Database backup successfully done", $val,$changelog);					  
		}
						
	}
       /* Amazon S3 Storage */
	if($profileStoragetype==2) {
		$profileData['amazon_secret_key'];
		if($checkBackuptype==0){
			$path=$backupDirPath."/".$db_file;
			$filepath=$backupDirPath."/".$backup_filename;
			Mage::getModel('backupsuite/amazons3')->calls3($db_file, $path, $profileData['amazon_access_key'], $profileData['amazon_secret_key'], $profileData['amazon_bucket']);
			Mage::getModel('backupsuite/amazons3')->calls3($backup_filename, $filepath, $profileData['amazon_access_key'], $profileData['amazon_secret_key'], $profileData['amazon_bucket']);
		}
		if($checkBackuptype==1){
			$filepath=$backupDirPath."/".$backup_filename;
			Mage::getModel('backupsuite/amazons3')->calls3($backup_filename, $filepath, $profileData['amazon_access_key'], $profileData['amazon_secret_key'], $profileData['amazon_bucket']);
		}
		if($checkBackuptype==2){
			$path=$backupDirPath."/".$db_file;
			Mage::getModel('backupsuite/amazons3')->calls3($db_file, $path, $profileData['amazon_access_key'], $profileData['amazon_secret_key'], $profileData['amazon_bucket']);
		}
	}
	/* Dropbox Storage */
	if($profileStoragetype==3) { 
		$filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'access_'.$profile_Id.'.token';					  	      
		if(is_file($filePath)) { 
			if($checkBackuptype==0){
				Mage::getModel('backupsuite/dropbox')->uploadToDropbox($db_file,$profile_Id);
				Mage::getModel('backupsuite/dropbox')->uploadToDropbox($backup_filename,$profile_Id);
			}
			if($checkBackuptype==1){
				Mage::getModel('backupsuite/dropbox')->uploadToDropbox($backup_filename,$profile_Id);
			}
			if($checkBackuptype==2){
				Mage::getModel('backupsuite/dropbox')->uploadToDropbox($db_file,$profile_Id);
			}
		}
	}
	/* Box Storage */
	if($profileStoragetype==4) {
		$filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.box';	      
		if(is_file($filePath)) { 
			if($checkBackuptype==0){
				Mage::getModel('backupsuite/boxnet')->uploadFileToBoxnet($db_file,$profile_Id);
				Mage::getModel('backupsuite/boxnet')->uploadFileToBoxnet($backup_filename,$profile_Id);
			}
			if($checkBackuptype==1){
				Mage::getModel('backupsuite/boxnet')->uploadFileToBoxnet($backup_filename,$profile_Id);
			}
			if($checkBackuptype==2){ 
				Mage::getModel('backupsuite/boxnet')->uploadFileToBoxnet($db_file,$profile_Id);
			}
		}
	}
	
	if( $profileData['delete_enable']!='0' ) { 
	    try{
		    if($profileData['delete_enable']==1){
			$data = Mage::getModel('backupsuite/backupsuite')->getMaxArchives($profileData['max_backups'],$profile_Id);
		    }
		    if($profileData['delete_enable']==2){
			$data = Mage::getModel('backupsuite/backupsuite')->getAllArchives($profileData['delete_days'],$profile_Id);
		    }
			foreach($data as $val)
			{
				if($val->getType() == 0) { //server
				    @unlink($backupDirPath."/".$val->getFilename());				
				    @unlink($backupDirPath."/".$val->getDbfilename());
				}
				if($val->getType() == 1) { //ftp
					$ftp_server_name = $profileData['ftp_server'];
					$conn = ftp_connect($ftp_server_name) or die("Could not connect");
					$ftp_user_name = $profileData['ftp_username'];					  
					$ftp_user_pass = $profileData['ftp_password'];
					ftp_login($conn, $ftp_user_name, $ftp_user_pass);
					ftp_delete($conn, $val->getFilename());
					ftp_close($conn);

				}
				if($val->getType() == 2) { //amazon s3
					Mage::getModel('backupsuite/amazons3')->removeS3($val->getFilename(), $profileData['amazon_bucket'], $profileData['amazon_access_key'], $profileData['amazon_secret_key']);					
				} 
				if($val->getType() == 3) {
					 	Mage::getModel('backupsuite/dropbox')->removeFromDropbox($val->getFilename(),$profile_Id);			
				} 
				if($val->getType() == 4) {
					Mage::getModel('backupsuite/boxnet')->removeFromBoxnet($val->getFilename(),$profile_Id);			
				} 
				if($val->getType() == 5) {
					Mage::getModel('backupsuite/gdriveauth')->removeFromGdrive($val->getFileId(),$val->getDbfileId(),$profile_Id);			
				} 
				Mage::getModel('backupsuite/backupsuite')->removefromDb($val->getId(),$profile_Id);
			}
		}catch(Exception $m){
		      $this->_delerrors[] = $m->getMessage();
		      $this->_delerrors[] = $m->getTrace();
		}
		$this->sendDeleteEmail($profile_Id);	
		
	}
	/* Unnecessary files remove from local server */
	if($profileData['backup_error_delete_local']==1){
 
	    $removezerofiles =glob($backupDirPath.'/' . "*",GLOB_BRACE);

	    foreach($removezerofiles as $Filezerolist){ 

		preg_match("/[^\/]+$/", $Filezerolist, $matches);
		//get filename
		preg_match('/^~/', $matches[0],$tr); 
		//Get File Extension 
 		$ext = pathinfo($matches[0], PATHINFO_EXTENSION);
		//Check Start with ~ & end with tar 
		if( count($tr)>0 && $ext=='tar'){ @unlink($Filezerolist); }
		//check Filesize zero
		if(filesize($Filezerolist)==0){ @unlink($Filezerolist); }
	    }
	}
	/* Enable Cache */
	if($checkcache==2){
	    $allTypes1 = Mage::app()->useCache();
	    Mage::app()->saveUseCache($allTypes1);
	}
  }
  catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
        }
      $this->sendBackupsuiteEmail($profile_Id);		

  }
  public function sendBackupsuiteEmail($profileId) { 
        if (count($this->_errors)>0) {
           
	      $profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($profileId);
	      $translate = Mage::getSingleton('core/translate');
	      $translate->setTranslateInline(false);

	      $toemails=explode(",",$profileData['success_recipient']);
	      $templateId = $profileData['success_template'];      
	      $mailSubject = 'Backupsuite Warnings: ';
	      $senderName=$profileData['success_sender'];
	      $senderEmail ='test@domain.com';    
	      $sender = array('name' => $senderName,'email' => $senderEmail);
	      
	      $storeId = Mage::app()->getStore()->getId();
	      $vars=array('warnings' => join("\n", $this->_errors));
	      $recepientName = 'John Doe';   
	      foreach($toemails as $toemail){
		$translate  = Mage::getSingleton('core/translate');
	      // Send Transactional Email
	      Mage::getModel('core/email_template')
	      ->setTemplateSubject($mailSubject)
	      ->sendTransactional($templateId, $sender, $toemail, null, $vars, $storeId);
		$translate->setTranslateInline(true); 
	    }
	}else{ 
	      $profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($profileId);
	      $translate = Mage::getSingleton('core/translate');
	      $translate->setTranslateInline(false);

	      $toemails=explode(",",$profileData['success_recipient']);
	      $templateId = $profileData['success_template'];      
	      $mailSubject = 'Backupsuite Success Message: ';
	      $senderName=$profileData['success_sender'];
	      $senderEmail ='test@domain.com';    
	      $sender = array('name' => $senderName,'email' => $senderEmail);
	      
	      $storeId = Mage::app()->getStore()->getId();
	      $vars=array('success' => 'Successfully Done');
	      $recepientName = 'John Doe';   
	      foreach($toemails as $toemail){
		$translate  = Mage::getSingleton('core/translate');
	      // Send Transactional Email
	      Mage::getModel('core/email_template')
	      ->setTemplateSubject($mailSubject)
	      ->sendTransactional($templateId, $sender, $toemail, null, $vars, $storeId);
		$translate->setTranslateInline(true); 
	    }
      
	}
    }
    public function sendDeleteEmail($profileId) { 
        if (count($this->_delerrors)>0) {
           
	      $profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($profileId);
	      $translate = Mage::getSingleton('core/translate');
	      $translate->setTranslateInline(false);

	      $toemails=explode(",",$profileData['delete_success_recipient']);
	      $templateId = $profileData['delete_success_template'];      
	      $mailSubject = 'Backupsuite Delete Warnings: ';
	      $senderName=$profileData['delete_success_sender'];
	      $senderEmail ='test@domain.com';    
	      $sender = array('name' => $senderName,'email' => $senderEmail);
	      
	      $storeId = Mage::app()->getStore()->getId();
	      $vars=array('errordeletemsg' => join("\n", $this->_delerrors));
	      $recepientName = 'John Doe';   
	      foreach($toemails as $toemail){
		$translate  = Mage::getSingleton('core/translate');
	      // Send Transactional Email
	      Mage::getModel('core/email_template')
	      ->setTemplateSubject($mailSubject)
	      ->sendTransactional($templateId, $sender, $toemail, null, $vars, $storeId);
		$translate->setTranslateInline(true); 
	    }
	}else{ 
	      $profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($profileId);
	      $translate = Mage::getSingleton('core/translate');
	      $translate->setTranslateInline(false);

	      $toemails=explode(",",$profileData['delete_success_recipient']);
	      $templateId = $profileData['delete_success_template'];      
	      $mailSubject = 'Backupsuite Delete Success Message: ';
	      $senderName=$profileData['delete_success_sender'];
	      $senderEmail ='test@domain.com';    
	      $sender = array('name' => $senderName,'email' => $senderEmail);
	      
	      $storeId = Mage::app()->getStore()->getId();
	      $vars=array('deletesuccessmsg' => 'Successfully Delete Backup');
	      $recepientName = 'John Doe';   
	      foreach($toemails as $toemail){
		$translate  = Mage::getSingleton('core/translate');
	      // Send Transactional Email
	      Mage::getModel('core/email_template')
	      ->setTemplateSubject($mailSubject)
	      ->sendTransactional($templateId, $sender, $toemail, null, $vars, $storeId);
		$translate->setTranslateInline(true); 
	    }
      
	}
    }


}
