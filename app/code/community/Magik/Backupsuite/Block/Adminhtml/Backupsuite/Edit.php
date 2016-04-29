<?php
	
class Magik_Backupsuite_Block_Adminhtml_Backupsuite_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_removeButton('reset');
				$this->_removeButton('delete');
				if( Mage::registry("backupsuite_data") && Mage::registry("backupsuite_data")->getId() ){
				    $this->_removeButton('save');
				}
				$this->_objectId = "id";
				$this->_blockGroup = "backupsuite";
				$this->_controller = "adminhtml_backupsuite";
				$this->_updateButton("save", "label", Mage::helper("backupsuite")->__("Backup Now"));
				/*$this->_updateButton("delete", "label", Mage::helper("backupsuite")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("backupsuite")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);*/



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("backupsuite_data") && Mage::registry("backupsuite_data")->getId() ){

				    return Mage::helper("backupsuite")->__("Edit  '%s'", $this->htmlEscape(Mage::registry("backupsuite_data")->getName()));

				} 
				else{

				     return Mage::helper("backupsuite")->__("Add Backup");

				}
		}
}