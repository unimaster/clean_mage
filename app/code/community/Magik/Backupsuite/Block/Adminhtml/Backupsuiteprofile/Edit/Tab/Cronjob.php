<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tab_Cronjob extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("backupsuite_form", array("legend"=>Mage::helper("backupsuite")->__("Cronjob information")));

				$fieldset->addField('cron_enable', 'select', array(
				'label' => Mage::helper('backupsuite')->__('Enable Cron Backup'),
				'name' => 'cron_enable',
				'values' => array(array('value' => '0','label' => Mage::helper('backupsuite')->__('No'),),
						  array('value' => '1','label' => Mage::helper('backupsuite')->__('Yes'),),
					    ),
			        ));

				$eventElempreview=$fieldset->addField('cron_type', 'select', array(
				'label' => Mage::helper('backupsuite')->__('Cron Expression'),
				'name' => 'cron_type',
				'values' => array(array('value' => 'def','label' => Mage::helper('backupsuite')->__('Default'),),
						  array('value' => 'cus','label' => Mage::helper('backupsuite')->__('Custom'),),
					    ),
				"onchange" => "applyforall()",
			        ));
				
				$fieldset->addType('cronsetting','Magik_Backupsuite_Varien_Data_Form_Element_Cronexpr');

				$fieldset->addField('cronsetting_div', 'cronsetting', array(
				  'name'      => 'cronsetting_div',
				));

				
				$fieldset->addField("success_recipient", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Email Recipient"),
				    "name" => "success_recipient",
				    "class" => "required-entry",
				    "required" => true,
				    'after_element_html' => '<br/><small>If you use multiple separate by comma.</small>',
				));
			      $fieldset->addField("success_sender", "text", array(
				    "label" => Mage::helper("backupsuite")->__("Email Sender"),
				    "name" => "success_sender",
				    "class" => "required-entry",
				    "required" => true,
				   
				));
				$emailTemplate = Mage::getModel('backupsuite/emailtemplate')->toOptionArray();
				$fieldset->addField('success_template', 'select', array(
				    'label' => 'Email Template',
				    'name'  => 'success_template',
				    'values' => $emailTemplate,
				)); 
				
				$eventElempreview->setAfterElementHtml('
				<script type="text/javascript">
				    function applyforall(){
					var elem = document.getElementById("cron_type");
					if(elem.value=="cus"){
					    document.getElementById("default_expr_area").style.display="none";
					    document.getElementById("custom_expr_area").style.display="block";
					}else{
					    document.getElementById("default_expr_area").style.display="block";
					    document.getElementById("custom_expr_area").style.display="none";
				      }
    

					}
				</script>');
				
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
