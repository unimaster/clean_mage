<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Logs extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Log information")));
				
				$fieldset->addField('note', 'note', array(
				    'text'     => Mage::helper('backupsuite')->__('Magento log folder (/var/www/magentofolder/var/log/magikbackuplog) will be used.'),
				    "label" => Mage::helper("backupsuite")->__("Profile Log Path"),
				));
				$fieldset->addField("logs_level", "select", array(
				    "label" => Mage::helper("backupsuite")->__("Log Level"),
				    "name" => "logs_level",
				    "value"=>"ALL",
				    "values" => array(array('value'=> "ALL",'label'=> Mage::helper('backupsuite')->__('All'),),
						      /*array('value'=> "INFO",'label'=> Mage::helper('backupsuite')->__('Info'),),*/
						      array('value'=> "WARNING",'label'=> Mage::helper('backupsuite')->__('Warnings & Errors'),),
						      array('value'=> "OFF",'label'=> Mage::helper('backupsuite')->__('Off'),),
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
