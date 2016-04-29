<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Storage extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("General Setting")));
				$reqid= $this->getRequest()->getParam("id");
				$fieldset->addField("db_host", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Host"),
				    "name" => "db_host",
				    "required" => false,
				));
				$fieldset->addField("db_username", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Username"),
				    "name" => "db_username",
				    "required" => false,
				));
				$fieldset->addField("db_password", "password", array(
				    "label" => Mage::helper("backupsuite")->__("Password"),
				    "name" => "db_password",
				    "required" => false,
				));
				$fieldset->addField("db_name", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Databse Name"),
				    "name" => "db_name",
				    "required" => true,
				    "class" => "required-entry",
				));
				$fieldset->addField("storage_type", "select", array(
				    "label" => Mage::helper("backupsuite")->__("Storage Apllication"),
				    "name" => "storage_type",
				    "value"=>0,
				    "values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('Local Storage'),),
						      array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('FTP'),),
						      array('value'=> 2,'label'=> Mage::helper('backupsuite')->__('Amazon S3'),),
						      array('value'=> 3,'label'=> Mage::helper('backupsuite')->__('Dropbox'),),
						      array('value'=> 4,'label'=> Mage::helper('backupsuite')->__('Box'),),
						      array('value'=> 5,'label'=> Mage::helper('backupsuite')->__('Google Drive'),),
						      ),
				  
				)); 
				
				$fieldset = $form->addFieldset("ftp_form", array("legend"=>Mage::helper("backupsuite")->__("FTP Settings"), "disabled" => true,));
				
				$fieldset->addField("ftp_storage", "select", array(
				    "label" => Mage::helper("backupsuite")->__("Create backup on FTP server"),
				    "name" => "ftp_storage",
				    "value"=>0,
				    "values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('No'),),
						      array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('Yes'),),
						      ),

				)); 
				$fieldset->addField("ftp_server", "text", array(
				    "label" => Mage::helper("backupsuite")->__("FTP Server"),
				    "name" => "ftp_server",
				    "required" => false,
				));
				$fieldset->addField("ftp_username", "text", array(
				    "label" => Mage::helper("backupsuite")->__("FTP Username"),
				    "name" => "ftp_username",
				    "required" => false,
				));
				$fieldset->addField("ftp_password", "password", array(
				    "label" => Mage::helper("backupsuite")->__("FTP Password"),
				    "name" => "ftp_password",
				    "required" => false,
				));
			      
				$fieldset = $form->addFieldset("amazon_form", array("legend"=>Mage::helper("backupsuite")->__("Amazon S3")));
				
				$fieldset->addField("amazon_storage", "select", array(
				    "label" => Mage::helper("backupsuite")->__("Create backup on amazon server"),
				    "name" => "amazon_storage",
				    "value"=>0,
				    "values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('No'),),
						      array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('Yes'),),
						      ),
				)); 
				$fieldset->addField("amazon_access_key", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Access Key"),
				    "name" => "amazon_access_key",
				    "required" => false,
				));
				$fieldset->addField("amazon_secret_key", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Secret Key"),
				    "name" => "amazon_secret_key",
				    "required" => false,
				));
				$fieldset->addField("amazon_bucket", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Bucket Name"),
				    "name" => "amazon_bucket",
				    "required" => false,
				));

				if($reqid !=''){
				    $fieldset = $form->addFieldset("dropbox_form", array("legend"=>Mage::helper("backupsuite")->__("Dropbox")));
				    $dboxType= $fieldset->addField("dropbox_storage", "select", array(
					"label" => Mage::helper("backupsuite")->__("Store backup to dropbox"),
					"name" => "dropbox_storage",
					"value"=>0,
					"values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('No'),),
							  array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('Yes'),),
							  ),
				    ));
				   $fieldset->addType('add_button', 'Magik_Backupsuite_Varien_Data_Form_Element_Dropboxbutton'); 
				 $dboxAuth= $fieldset->addField('dropbox_oauth_button_'.$reqid, 'add_button', array(
					  'label' => Mage::helper('backupsuite')->__('Authorize Dropbox Account'),
					  'id' => 'dropbox_oauth_button_'.$reqid,
					 "name" => "dropbox_oauth_button_".$reqid,				      
				      ));
				}

				if($reqid !=''){
				    $fieldset = $form->addFieldset("box_form", array("legend"=>Mage::helper("backupsuite")->__("Box")));
				   $boxType=$fieldset->addField("box_storage", "select", array(
					"label" => Mage::helper("backupsuite")->__("Store backup to Box.net"),
					"name" => "box_storage",
					"value"=>0,
					"values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('No'),),
							  array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('Yes'),),
							  ),
				    ));
				   $fieldset->addType('add_button', 'Magik_Backupsuite_Varien_Data_Form_Element_Boxbutton'); 
				  $boxAuth=$fieldset->addField('box_oauth_button_'.$reqid, 'add_button', array(
					  'label' => Mage::helper('backupsuite')->__('Authorize Box.net Account'),
					  'id' => 'box_oauth_button_'.$reqid,
					"name" => "box_oauth_button_".$reqid,				      
				      ));
				}

				if($reqid !=''){
				    $fieldset = $form->addFieldset("gdrive_form", array("legend"=>Mage::helper("backupsuite")->__("Google Drive")));
				  $gdriveType=  $fieldset->addField("gdrive_storage", "select", array(
					"label" => Mage::helper("backupsuite")->__("Store backup to Google Drive"),
					"name" => "gdrive_storage",
					"value"=>0,
					"values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('No'),),
							  array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('Yes'),),
							  ),
				    ));
				   $fieldset->addType('add_button', 'Magik_Backupsuite_Varien_Data_Form_Element_Gdrivebutton'); 
				  $gdriveAuth= $fieldset->addField('gdrive_oauth_button_'.$reqid, 'add_button', array(
					  'label' => Mage::helper('backupsuite')->__('Authorize Google Drive Account'),
					  'id' => 'gdrive_oauth_button_'.$reqid,
					   "name" => "gdrive_oauth_button",				      
				      ));
				 $gdriveRemove= $fieldset->addField("gdrive_authcode", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Google Drive Auth Code"),
				    "name" => "gdrive_authcode",
				    "required" => false,
				  ));
				}

				



				if (Mage::getSingleton("adminhtml/session")->getBackupsuiteprofileData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBackupsuiteprofileData());
					Mage::getSingleton("adminhtml/session")->setBackupsuiteprofileData(null);
				} 
				elseif(Mage::registry("backupsuiteprofile_data")) {
				    $form->setValues(Mage::registry("backupsuiteprofile_data")->getData());
				}
				return parent::_prepareForm();
		}
}
