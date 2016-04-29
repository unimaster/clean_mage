<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuite_Renderer_Profilename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function render(Varien_Object $row)   {

	$profileName = Mage::getModel("backupsuite/backupsuiteprofile")->load($row->getProfileId());
	return $profileName->getName();	
    }
}
