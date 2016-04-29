<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Filesexclusive extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Files and Directories Exclusion")));

			     /*$profileType = Mage::getModel('backupsuite/config_filestructure')->toOptionArray();
				$fieldset->addField('filesexclusion', 'multiselect', array(
				    'label' => 'Filesexclusion',
				    'name'  => 'filesexclusion',
				    'values' => $profileType,
				)); */

				
				   $fieldset->addType('filesexclusion','Magik_Backupsuite_Varien_Data_Form_Element_Filesexclusion');

				    $fieldset->addField('filesexclusion', 'filesexclusion', array(
					'name'      => 'filesexclusion',
					//"label" => Mage::helper("backupsuite")->__("Filesexclusion"),
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
