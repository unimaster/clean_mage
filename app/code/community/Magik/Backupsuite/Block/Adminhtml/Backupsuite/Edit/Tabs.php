<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuite_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("backupsuite_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("backupsuite")->__("Backup Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("backupsuite")->__("Backup Information"),
				"title" => Mage::helper("backupsuite")->__("Backup Information"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuite_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
