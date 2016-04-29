<?php
class Magik_Backupsuite_Block_Backupsuite extends Mage_Core_Block_Template {
    
    public function _prepareLayout() {
		return parent::_prepareLayout();
    }
    
    public function getBackupsuite() { 
        
	if (!$this->hasData('backupsuite')) {
            $this->setData('backupsuite', Mage::registry('backupsuite'));
        }
        return $this->getData('backupsuite');
    }
}