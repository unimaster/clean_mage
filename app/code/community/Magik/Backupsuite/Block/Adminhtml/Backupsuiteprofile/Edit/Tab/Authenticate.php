<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Authenticate extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Authentication settings")));
				
				$authenable=$fieldset->addField("auth_enable", "select", array(
				    "label" => Mage::helper("backupsuite")->__("Enable"),
				    "name" => "auth_enable",
				    "value"=>0,
				    "values" => array(array('value'=> 0,'label'=> Mage::helper('backupsuite')->__('No'),),
						      array('value'=> 1,'label'=> Mage::helper('backupsuite')->__('Yes'),),
						     ),
				  "after_element_html" => "<small><br/>Enable and fill in the settings below in case the site is protected with Apache authentication</small>",
				)); 
				$authuser=$fieldset->addField("auth_user", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Username"),
				    "name" => "auth_user",
				    
				));
				$authpass=$fieldset->addField("auth_pass", "password", array(
				    "label" => Mage::helper("backupsuite")->__("Password"),
				    "name" => "auth_pass",
				    
				));

				if (Mage::getSingleton("adminhtml/session")->getBackupsuiteprofileData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBackupsuiteprofileData());
					Mage::getSingleton("adminhtml/session")->setBackupsuiteprofileData(null);
				} 
				elseif(Mage::registry("backupsuiteprofile_data")) {
				    $form->setValues(Mage::registry("backupsuiteprofile_data")->getData());
				}
				$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
				->addFieldMap($authenable->getHtmlId(), $authenable->getName())
				->addFieldMap($authuser->getHtmlId(), $authuser->getName())
				->addFieldMap($authpass->getHtmlId(), $authpass->getName())
				->addFieldDependence($authuser->getName(),$authenable->getName(),'1')
				->addFieldDependence($authpass->getName(),$authenable->getName(),'1')
				
			    );
				return parent::_prepareForm();
		}
}
