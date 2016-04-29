<?php
	
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "backupsuite";
				$this->_controller = "adminhtml_backupsuiteprofile";
				$this->_updateButton("save", "label", Mage::helper("backupsuite")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("backupsuite")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("backupsuite")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("backupsuiteprofile_data") && Mage::registry("backupsuiteprofile_data")->getId() ){

				    return Mage::helper("backupsuite")->__("Edit Profile '%s'", $this->htmlEscape(Mage::registry("backupsuiteprofile_data")->getName()));

				} 
				else{

				     return Mage::helper("backupsuite")->__("Add Profile");

				}
		}
}