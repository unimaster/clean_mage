<?php
class Magik_Backupsuite_Adminhtml_Backupsuite_BackupsuiteController extends Mage_Adminhtml_Controller_Action
{
	protected $_errors = array();
	protected $_delerrors = array();	
	protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("backupsuite/backupsuite")->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuite  Manager"),Mage::helper("adminhtml")->__("Backupsuite Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Backupsuite"));
			    $this->_title($this->__("Manager Backupsuite"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Backupsuite"));
				$this->_title($this->__("Backupsuite"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("backupsuite/backupsuite")->load($id);
				if ($model->getId()) {
					Mage::register("backupsuite_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("backupsuite/backupsuite");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuite Manager"), Mage::helper("adminhtml")->__("Backupsuite Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuite Description"), Mage::helper("adminhtml")->__("Backupsuite Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuite_edit"))->_addLeft($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuite_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("backupsuite")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Backupsuite"));
		$this->_title($this->__("Backupsuite"));
		$this->_title($this->__("New Item"));

                $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("backupsuite/backupsuite")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("backupsuite_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("backupsuite/backupsuite");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuite Manager"), Mage::helper("adminhtml")->__("Backupsuite Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuite Description"), Mage::helper("adminhtml")->__("Backupsuite Description"));


		$this->_addContent($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuite_edit"))->_addLeft($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuite_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{
		    $this->_errors = array();
		    $this->_delerrors = array();
		    $post_data=$this->getRequest()->getPost();

			if ($post_data) {
				try {	
					$profile_Id=$post_data['profile_id'];
					$profileData =Mage::getModel('backupsuite/backupsuiteprofile')->load($post_data['profile_id']);
					$checkBackuptype=$profileData['type'];
					
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
					if($profileData['path']!=''){ $backupDirPath=$profileData['path'];
					}else{ $backupDirPath = Mage::getBaseDir().DS.'var'.DS.'magikbackup';}

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
					    Mage::getSingleton('adminhtml/session')->addError('No Free Space for backup');
					    Mage::getSingleton('adminhtml/session')->setNewsData($this->getRequest()->getPost());
					    $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
					    return;
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
					if($checkBackuptype==0) {
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
						  Mage::log('Finish getting files for archive', $val,$changelog);
						  Mage::log( 'Finish packing files', $val,$changelog);
						  Mage::log( 'Finish directories and files backup', $val,$changelog);
						  Mage::log( 'Database Backup was successfully saved', $val,$changelog);
						  Mage::log( 'Database Backup process finished', $val,$changelog);
						  /* Files Exclusion */
						  $IgnorePathList=array();	
						  $BackupIgnorePaths=$profileData['filesexclusion'];
						  $AllIgnorePaths=explode(',',$BackupIgnorePaths);
						  foreach($AllIgnorePaths as $All_IgnorePaths){
						      array_push($IgnorePathList,Mage::getBaseDir() . DS.$All_IgnorePaths);
						  }

						  $type="filesystem";
						  $fileExt='tgz';
						      $tm=time();
						      
						  Mage::log('Files backup process Started', $val,$changelog);
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
						      Mage::log('Files backup process finished', $val,$changelog);
						      if($profileData['storage_type']==5) {
							  $filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.json';
							    if(is_file($filePath)) { 
								  $dbID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($db_file,$profile_Id);
								  $fileID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($backup_filename,$profile_Id);
								  $post_data['file_id']=$fileID;
							          $post_data['dbfile_id']=$dbID;
							    }
							    
						      }
						      $post_data['created']=now();
						      $post_data['type']=0;
						      $post_data['storage_type']=$profileData['storage_type'];
						      $post_data['filename']=$backup_filename;
						      $post_data['dbfilename']=$db_file;
						      $post_data['log_detail']=$changelog;
						  $model = Mage::getModel("backupsuite/backupsuite")
						  ->addData($post_data)
						  ->setId($this->getRequest()->getParam("id"))
						  ->save();
						  
						 
					}
					/* Create File backup */
					if($checkBackuptype==1) {
						    /* Files Exclusion */
						      $IgnorePathList=array();	
						      $BackupIgnorePaths=$profileData['filesexclusion'];
						      $AllIgnorePaths=explode(',',$BackupIgnorePaths);
						      foreach($AllIgnorePaths as $All_IgnorePaths){
							    array_push($IgnorePathList,Mage::getBaseDir() . DS.$All_IgnorePaths);
						      }
						   
						      $type="filesystem";
						      $fileExt='tgz';
						      $tm=time();
						      
						      Mage::log('Files backup process Started', $val,$changelog);
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
						       Mage::log('Files backup process finished', $val,$changelog);
						       if($profileData['storage_type']==5) {
							  $filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.json';
							    if(is_file($filePath)) { 
								  $fileID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($backup_filename,$profile_Id);
								  $post_data['file_id']=$fileID;
							    }
						       }
						      $post_data['created']=now();
						      $post_data['type']=0;
						      $post_data['storage_type']=$profileData['storage_type'];
						      $post_data['filename']=$backup_filename;
						      $post_data['log_detail']=$changelog;
						      $model = Mage::getModel("backupsuite/backupsuite")
						      ->addData($post_data)
						      ->setId($this->getRequest()->getParam("id"))
						      ->save();

						     
					}
					
					if($checkBackuptype==2){
						    
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
						    Mage::log('Finish getting files for archive', $val,$changelog);
						    
						    if($profileData['storage_type']==5) {
							  $filePath=Mage::getBaseDir().DS.'var'.DS.'data'.DS.'token_'.$profile_Id.'.json';
							    if(is_file($filePath)) { 
								 $dbID=Mage::getModel('backupsuite/gdriveauth')->uploadToGdrive($db_file,$profile_Id);
								  $post_data['dbfile_id']=$dbID;
							    }
						    }
						    Mage::log( 'Finish packing files', $val,$changelog);
						    Mage::log( 'Finish directories and files backup', $val,$changelog);
						    Mage::log( 'Backup was successfully saved', $val,$changelog);
						    Mage::log( 'Backup process finished', $val,$changelog);
						    $post_data['created']=now();
						    $post_data['type']=0;
						    $post_data['storage_type']=$profileData['storage_type'];
						    $post_data['dbfilename']=$db_file;
						    $post_data['log_detail']=$changelog;
						    $model = Mage::getModel("backupsuite/backupsuite")
							    ->addData($post_data)
							    ->setId($this->getRequest()->getParam("id"))
							    ->save();
						    
					}
					// FTP Storage
					if($profileData['storage_type']==1) {
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
									  
							  }else{ $ftplogdetails= "Connected to $ftp_server_name, for user $ftp_user_name";
								  Mage::log( $ftplogdetails, $val,$changelog);}
								
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
							}
							if($checkBackuptype==1){
								  $storefile = $storefileatt;
								  $fp = fopen($storefile, 'r') or die ("couldn't open!");
								  $storedestination_file = $storefilename;
								  $storesource_file = $storefileatt;
								  $upload = ftp_put ( $conn_id , $storedestination_file , $storesource_file , FTP_BINARY );				
								  ftp_close ( $conn_id ); 
							}
							if($checkBackuptype==2){
								  $file = $fileatt;
								  $fp = fopen($file, 'r') or die ("couldn't open!");
								  $destination_file = $filename;
								  $source_file = $fileatt;
								  $upload = ftp_put ( $conn_id , $destination_file , $source_file , FTP_BINARY );				
								  ftp_close ( $conn_id ); 
							}
						}
						/* Amazon S3 Storage */
						if($profileData['storage_type']==2) {
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
						if($profileData['storage_type']==3) { 
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
						if($profileData['box_storage']==4) {
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
						/* Delete backup files & db */
						if($profileData['delete_enable']!='0') {
						  try{
							if($profileData['delete_enable']==1){ //rotation
							  $deleteData = Mage::getModel('backupsuite/backupsuite')->getMaxArchives($profileData['max_backups'],$profile_Id);
							}
							if($profileData['delete_enable']==2){ //days
							  $deleteData = Mage::getModel('backupsuite/backupsuite')->getAllArchives($profileData['delete_days'],$profile_Id);
							}

							foreach($deleteData as $val) {
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

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Backupsuite was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setBackupsuiteData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->sendBackupsuiteEmail($profile_Id);
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						$this->_errors[] = $e->getMessage();
						$this->_errors[] = $e->getTrace();
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setBackupsuiteData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}
				   $this->sendBackupsuiteEmail($profile_Id);	
				}
				$this->_redirect("*/*/");
		}


		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("backupsuite/backupsuite");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("backupsuite/backupsuite");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'backupsuite.csv';
			$grid       = $this->getLayout()->createBlock('backupsuite/adminhtml_backupsuite_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'backupsuite.xml';
			$grid       = $this->getLayout()->createBlock('backupsuite/adminhtml_backupsuite_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
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


/**
     * Return some checking result
     *
     * @return void
     */
    public function checkAction()
    {
        Zend_Loader::loadClass('DropboxClient');
        
        $dropbox = new DropboxClient(array(
          'app_key' => Mage::getStoreConfig('settings/dropboxappkey'), 
          'app_secret' => Mage::getStoreConfig('settings/dropboxappsecret'),
          'app_full_access' => false,
        ),'en');
   
       
        if(!empty($_GET['auth_callback'])) // are we coming from dropbox's auth page?
        {             
            $request_token = Mage::getModel('backupsuite/dropbox')->load_token($_GET['oauth_token']);
            if(empty($request_token)) die('Request token not found!');
            
            // get & store access token, the request token is not needed anymore
            $access_token = $dropbox->GetAccessToken($request_token);
	    $profileId=$this->getRequest()->getParams('pid'); 
            $accessName= 'access_'.$profileId['pid'];
            Mage::getModel('backupsuite/dropbox')->store_token($access_token, $accessName);
            Mage::getModel('backupsuite/dropbox')->delete_token($_GET['oauth_token']);
	    $returnUrl = Mage::helper("adminhtml")->getUrl("backupsuite/adminhtml_backupsuiteprofile/index/");
	    Mage::app()->getResponse()->setRedirect($returnUrl);

        } else { 

           $return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?auth_callback=1";
           //$return_url = Mage::helper('adminhtml')->getUrl('backups/adminhtml_backups/index')."?auth_callback=1";
           $auth_url = $dropbox->BuildAuthorizeUrl($return_url);
           $request_token = $dropbox->GetRequestToken();
           Mage::getModel('backupsuite/dropbox')->store_token($request_token, $request_token['t']);
           ?>
            <script type="text/javascript">
            window.location.href="<?php echo $auth_url; ?>";
            </script>

          <?php
        }        
        
    }  

    public function removedropboxuserAction()
    { 
	$profileId=$this->getRequest()->getParams('pid'); 
	$dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
	@unlink($dirPath."/access_".$profileId['pid'].".token");
    }

    public function boxvalidAction()
    {
      	Zend_Loader::loadClass('BoxAPI');	
      	$client_id   = Mage::getStoreConfig('settings/boxnetappkey');
        $client_secret  = Mage::getStoreConfig('settings/boxnetsecret');
      	$redirect_uri 	= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      		
      	$box = new BoxAPI($client_id, $client_secret, $redirect_uri);
      	$profileId=$this->getRequest()->getParams('pid'); 
	$profileId['pid'];
      	if(!$box->load_token()){ 
      		if(isset($_GET['code'])){
      			$token = $box->get_token($_GET['code'], true);

      			if($box->write_token($token, 'file',$profileId['pid'])){
      				$box->load_token($profileId['pid']);
      			}
      		} else { 
      			$box->get_code();

      		}
      	}

      	if(isset($_GET['state']) && isset($_GET['code'])) {
                 // $returnUrl = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/magik_backups_config");
		  $returnUrl = Mage::helper("adminhtml")->getUrl("backupsuite/adminhtml_backupsuiteprofile/index/");
                  Mage::app()->getResponse()->setRedirect($returnUrl);
        }

    }

    public function removeboxuserAction(){	
	$profileId=$this->getRequest()->getParams('pid'); 
	$filename='/token_'.$profileId['pid'].'.box';
    	 $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
	
    	 @unlink($dirPath.$filename);       
    }

    public function gdrivevalidAction()
    {
	    $profileId=$this->getRequest()->getParams('pid');
	    require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Client.php');
	    require_once(Mage::getBaseDir('lib') . '/googledrive/Google_DriveService.php');
	    require_once(Mage::getBaseDir('lib') . '/googledrive/Google_Oauth2Service.php');
	    
	    // initialise the client
	    $client = new Google_Client();
	    $oauth2 = new Google_Oauth2Service($client);
	    // Get your credentials from the APIs Console
	    $client->setClientId(Mage::getStoreConfig('settings/gdriveappkey'));
	    $client->setClientSecret(Mage::getStoreConfig('settings/gdrivesecret'));
	    $client->setRedirectUri(Mage::getStoreConfig('settings/gdriveredirect'));
	    $client->setScopes(array('https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/userinfo.profile'));

	    $service = new Google_DriveService($client);
	    $authCode =Mage::getModel('backupsuite/backupsuiteprofile')->load($profileId['pid'])->getData('gdrive_authcode');
	    $client->setAccessToken($this->authenticate($client,$oauth2,$authCode,$profileId['pid']));
	

    }


    public function authenticate($client,$oauth2,$authCode='',$get_profileId){

      	$file = Mage::getBaseDir().DS.'var'.DS.'data'.'/token_'.$get_profileId.'.json';
      	if (file_exists($file)) return file_get_contents($file);
      	
      	//$_GET['code'] = ''; // insert the verification code here
       
      	// print the authentication URL
      	if ($authCode=='') {
      		header("Location:".$client->createAuthUrl(array('https://www.googleapis.com/auth/drive.file')));
      		exit;
      	}
       
      	file_put_contents($file, $client->authenticate($authCode,$get_profileId));

      	// if($client->getAccessToken()){ 
      	//     //echo ">>".$user = $oauth2->userinfo->get();
      	// }
      
	$resource = Mage::getSingleton('core/resource');
	$writeConnection = $resource->getConnection('core_write');
	$tableName = $resource->getTableName('backupsuiteprofile');
	$query = "UPDATE $tableName SET gdrive_authcode = '' WHERE id='".$get_profileId."'";
	$writeConnection->query($query);
      	exit;    
		
    }

    public function gdriveremoveAction(){
	$profileId=$this->getRequest()->getParams('pid'); 
        $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
	$filename='/token_'.$profileId['pid'].'.json';
        @unlink($dirPath.$filename); 

    }

    public function calspaceAction(){

	$params = $this->getRequest()->getParams();
	$baseDir=Mage::getBaseDir();
	$folder=$params['path'];
	$cmd="du -sh ".$baseDir.'/'.$folder." |xargs | cut -d ' ' -f 1";
	echo $t=exec($cmd);	
    }
    public function caltablespaceAction(){
      $params = $this->getRequest()->getParams();
      $TABLE_NAME=$params['path'];
      $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
      $sql = "SELECT TABLE_NAME,round(((data_length + index_length) / 1024 / 1024 ), 2) Size FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname' AND table_name = '$TABLE_NAME'";
      $collection = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
      echo $collection[0]['Size'].' Mb';
	
    }
   

}