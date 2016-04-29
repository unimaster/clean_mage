<?php
class Magik_Backupsuite_Model_Mysql4_Backupsuite extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("backupsuite/backupsuite", "id");
    }
}