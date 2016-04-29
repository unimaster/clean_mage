<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Deletesetting extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Delete settings")));
				
				$enabledelete=$fieldset->addField('delete_enable', 'select', array(
				'label' => Mage::helper('backupsuite')->__('Backup delete type'),
				'name' => 'delete_enable',
				'values' => array(array('value' => '0','label' => Mage::helper('backupsuite')->__('Disable'),),
						  array('value' => '1','label' => Mage::helper('backupsuite')->__('Rotation'),),
						  array('value' => '2','label' => Mage::helper('backupsuite')->__('Delete old'),),
					    ),
			        ));
				$maxbackups=$fieldset->addField('max_backups', 'text', array(
				'label' => Mage::helper('backupsuite')->__('Max number of backups'),
				'name' => 'max_backups',
				
			        ));
				$deletedays=$fieldset->addField('delete_days', 'text', array(
				'label' => Mage::helper('backupsuite')->__('Delete archives older than'),
				'name' => 'delete_days',
				
			        ));

				$successrecipient=$fieldset->addField("delete_success_recipient", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Success Email Recipient"),
				    "name" => "delete_success_recipient",
				    "required" => false,
				    'after_element_html' => '<br/><small>If you use multiple separate by comma.</small>',
				));
				$successsender=$fieldset->addField("delete_success_sender", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Success Email Sender"),
				    "name" => "delete_success_sender",
				    "required" => false,
				   
				));
				
				$emailTemplate = Mage::getModel('backupsuite/deleteemailtemplate')->toOptionArray();
				$fieldset->addField('delete_success_template', 'select', array(
				    'label' => 'Success Email Template',
				    'name'  => 'delete_success_template',
				    'values' => $emailTemplate,
				)); 

				$fieldset->addField("delete_error_recipient", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Error Email Recipient"),
				    "name" => "delete_error_recipient",
				    'after_element_html' => '<br/><small>If you use multiple separate by comma.</small>',
				));
				$fieldset->addField("delete_error_sender", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Error Email Sender"),
				    "name" => "delete_error_sender",
				     "required" => false,
				));
				$errorTemplate = Mage::getModel('backupsuite/deleteerrortemplate')->toOptionArray();
				$fieldset->addField('delete_error_template', 'select', array(
				    'label' => Mage::helper("backupsuite")->__("Error Email Template"),
				    'name'  => 'delete_error_template',
				    'values' => $errorTemplate,
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
				->addFieldMap($enabledelete->getHtmlId(), $enabledelete->getName())
				->addFieldMap($maxbackups->getHtmlId(), $maxbackups->getName())
				->addFieldMap($deletedays->getHtmlId(), $deletedays->getName())
				//->addFieldMap($successrecipient->getHtmlId(), $successrecipient->getName())
				//->addFieldMap($successsender->getHtmlId(), $successsender->getName())

				->addFieldDependence($maxbackups->getName(),$enabledelete->getName(),'1')
				->addFieldDependence($deletedays->getName(),$enabledelete->getName(),'2')
				//->addFieldDependence($successrecipient->getName(),$enabledelete->getName(),array(1,2))
				//->addFieldDependence($successsender->getName(),$enabledelete->getName(),array(1,2))

				/*->addFieldDependence($successrecipient->getName(),$enabledelete->getName(),'2')
				->addFieldDependence($successsender->getName(),$enabledelete->getName(),'1')
				->addFieldDependence($successsender->getName(),$enabledelete->getName(),'2')*/
				);
				return parent::_prepareForm();
		}
}
