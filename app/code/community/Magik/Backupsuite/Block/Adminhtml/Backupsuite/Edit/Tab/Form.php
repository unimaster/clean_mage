<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuite_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Backup information")));
				
				$reqid= $this->getRequest()->getParam("id");
				$profile_select = Mage::getModel('backupsuite/profilelist')->toOptionArray();
				$fieldset->addField('profile_id', 'select', array(
					'name'     => 'profile_id',
					'label'    => Mage::helper('backupsuite')->__('Profile'),
					'title'    => Mage::helper('backupsuite')->__('Profile'), 
					'required' => true,
					'values'   => $profile_select,
					
					));
				$fieldset->addField("name", "text", array(
				  "label" => Mage::helper("backupsuite")->__("Name"),
				  "name" => "name",
				));
				$fieldset->addField('description', 'textarea', array(
				  'label'     => Mage::helper('backupsuite')->__('Backup Description'),
				  'name'      => 'description',
				));
				
				if($reqid !=''){
				    
				    
				    $fieldset = $form->addFieldset("form-localfiles", array("legend"=>Mage::helper("backupsuite")->__("Backup Files")));
				    $fieldset->addType('localfiles','Magik_Backupsuite_Varien_Data_Form_Element_Localfiles');

				    $fieldset->addField('localfiles_div', 'localfiles', array(
					'name'      => 'localfiles_div',
					"label" => Mage::helper("backupsuite")->__("Local Files"),
				    ));

				    $fieldset = $form->addFieldset("form-log", array("legend"=>Mage::helper("backupsuite")->__("Backup Logs")));
				    $fieldset->addType('logfiles','Magik_Backupsuite_Varien_Data_Form_Element_Log');

				    $fieldset->addField('logfiles_div', 'logfiles', array(
					'name'      => 'logfiles_div',
					"label" => Mage::helper("backupsuite")->__("Backup Log"),
				    ));
				
				}

				if (Mage::getSingleton("adminhtml/session")->getBackupsuiteData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBackupsuiteData());
					Mage::getSingleton("adminhtml/session")->setBackupsuiteData(null);
				} 
				elseif(Mage::registry("backupsuite_data")) {
				    $form->setValues(Mage::registry("backupsuite_data")->getData());
				}
				return parent::_prepareForm();
		}
}
