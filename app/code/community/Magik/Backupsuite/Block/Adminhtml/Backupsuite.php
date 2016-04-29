<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuite extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct()
	{

	$this->_controller = "adminhtml_backupsuite";
	$this->_blockGroup = "backupsuite";
	$this->_headerText = Mage::helper("backupsuite")->__("Backup Manager");
	$this->_addButtonLabel = Mage::helper("backupsuite")->__("Create Backup");

	parent::__construct();
	
	}

}