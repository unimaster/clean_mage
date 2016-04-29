<?php
class Magik_Backupsuite_Model_Config_Backuptype
{
    public function toOptionArray()
    {
        return array(
	      array('value'=>0, 'label'=>Mage::helper('backupsuite')->__('Files and Database')),
	     array('value'=>1, 'label'=>Mage::helper('backupsuite')->__('Files only')),
             array('value'=>2, 'label'=>Mage::helper('backupsuite')->__('Database only')),
            
        );
    }

}
