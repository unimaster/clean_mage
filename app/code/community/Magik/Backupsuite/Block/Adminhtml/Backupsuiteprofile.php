<?php


class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_backupsuiteprofile";
	$this->_blockGroup = "backupsuite";
	$this->_headerText = Mage::helper("backupsuite")->__("Profile Manager");
	$this->_addButtonLabel = Mage::helper("backupsuite")->__("Add New Profile");
	parent::__construct();
	
	}

}