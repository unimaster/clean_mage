<?php
class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("backupsuiteprofile_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("backupsuite")->__("Profile Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("backupsuite")->__("General"),
				"title" => Mage::helper("backupsuite")->__("General"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_form")->toHtml(),
				));
				if( Mage::registry("backupsuiteprofile_data") && Mage::registry("backupsuiteprofile_data")->getId() ){
				$this->addTab("form_storage", array(
				"label" => Mage::helper("backupsuite")->__("Storage"),
				"title" => Mage::helper("backupsuite")->__("Storage"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_storage")->toHtml(),
				));
				}
				$this->addTab("form_dbexclusive", array(
				"label" => Mage::helper("backupsuite")->__("DB Tables Exclusion"),
				"title" => Mage::helper("backupsuite")->__("DB Tables Exclusion"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_dbexclusive")->toHtml(),
				));
				$this->addTab("form_filesexclusive", array(
				"label" => Mage::helper("backupsuite")->__("Files Exclusion"),
				"title" => Mage::helper("backupsuite")->__("Files Exclusion"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_filesexclusive")->toHtml(),
				));
				$this->addTab("form_cronjob", array(
				"label" => Mage::helper("backupsuite")->__("Cron Job"),
				"title" => Mage::helper("backupsuite")->__("Cron Job"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_cronjob")->toHtml(),
				));
				$this->addTab("form_delete", array(
				"label" => Mage::helper("backupsuite")->__("Delete Settings"),
				"title" => Mage::helper("backupsuite")->__("Delete Settings"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_deletesetting")->toHtml(),
				));
				$this->addTab("form_logs", array(
				"label" => Mage::helper("backupsuite")->__("Logs"),
				"title" => Mage::helper("backupsuite")->__("Logs"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_logs")->toHtml(),
				));
				/*$this->addTab("form_auth", array(
				"label" => Mage::helper("backupsuite")->__("Authentication"),
				"title" => Mage::helper("backupsuite")->__("Authentication"),
				"content" => $this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tab_authenticate")->toHtml(),
				));*/
				return parent::_beforeToHtml();
		}

}
