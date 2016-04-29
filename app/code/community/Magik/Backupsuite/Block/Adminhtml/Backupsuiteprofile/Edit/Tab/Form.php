<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Profile information")));

				$fieldset->addField("name", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Name"),
				    "name" => "name",
				    "class" => "required-entry",
				    "required" => true,
				));
				$fieldset->addField("suffix", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Backup Filename Suffix"),
				    "name" => "suffix",
				    "required" => false,
				    "class" =>"validate-length maximum-length-10 minimum-length-1 validate-alphanum",
				    "after_element_html" => "<small><br/>Max 10 characters and Please use only letters (a-z or A-Z) or numbers (0-9) only in this field. No spaces or other characters are allowed'</small>",
				));	
				
				$profileType = Mage::getModel('backupsuite/config_backuptype')->toOptionArray();
				$fieldset->addField('type', 'select', array(
				    'label' => 'Profile Type',
				    'name'  => 'type',
				    'values' => $profileType,
				)); 
				$fieldset->addField("path", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Profile Backup Path"),
				    "name" => "path",
				    "required" => false,
				    'after_element_html' => '<small><br>If empty, Magento backup folder (/var/www/domain.com/var/magikbackup) will be used.</small>',
				));
				$fieldset->addField("free_disk_space", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Total Free Space Before Backup Start"),
				    "name" => "free_disk_space",
				    "after_element_html" => "<small><br/>In Mb. If empty, free space will not be checked.</small>",
				));
				$fieldset->addField('backup_error_delete_local', 'select', array(
				'label' => Mage::helper('backupsuite')->__('Delete unnesessary files from local server'),
				'name' => 'backup_error_delete_local',
				'values' => array(array('value' => '0','label' => Mage::helper('backupsuite')->__('No'),),
						  array('value' => '1','label' => Mage::helper('backupsuite')->__('Yes'),),
					    ),
				"after_element_html" => "<small><br/>Delete unnesessary backup files from local server if backup process has errors.</small>",

			        ));
				$fieldset->addField('disable_cache', 'select', array(
				'label' => Mage::helper('backupsuite')->__('Disable cache during backup process'),
				'name' => 'disable_cache',
				'values' => array(array('value' => '0','label' => Mage::helper('backupsuite')->__('No'),),
						  array('value' => '1','label' => Mage::helper('backupsuite')->__('Yes'),),
					    ),

			        ));

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
