<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Dbexclusive extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("DB Tables Exclusive")));

				/*$profileType = Mage::getModel('backupsuite/config_dbstructure')->toOptionArray();
				$fieldset->addField('dbexclusion', 'multiselect', array(
				    'label' => 'dbexclusion',
				    'name'  => 'dbexclusion',
				    'values' => $profileType,
				)); */

				$fieldset->addType('dbexclusion','Magik_Backupsuite_Varien_Data_Form_Element_Dbexclusion');
				$fieldset->addField('dbexclusion', 'dbexclusion', array(
					'name'      => 'dbexclusion',
					//"label" => Mage::helper("backupsuite")->__("Dbexclusion"),
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
