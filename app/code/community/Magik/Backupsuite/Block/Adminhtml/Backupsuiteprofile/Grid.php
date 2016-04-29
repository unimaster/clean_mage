<?php

class Magik_Backupsuite_Block_Adminhtml_Backupsuiteprofile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("backupsuiteprofileGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("backupsuite/backupsuiteprofile")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("backupsuite")->__("ID"),
				"align" =>"right",
				"width" => "50px",
				"type" => "number",
				"index" => "id",
				));
				$this->addColumn('name', array(
				  'header' => Mage::helper("backupsuite")->__('Profile Name'),
				  'align' => 'left',
				  'index' => 'name',
				));
				$this->addColumn("storage_type", array(
				      "header"    => Mage::helper("backupsuite")->__("Storage Application"),
				      "align"     => "left",
				      "index"     => "storage_type",
				      "type"      => "options",
				      "options"   => array( 0 => "Local Storage",1 => "FTP", 2 => "Amazon S3",3 => "Dropbox",4 => "Box",5=>"Google Drive",
				      ),
				  ));
				$this->addColumn("type", array(
				      "header"    => Mage::helper("backupsuite")->__("Profile Type"),
				      "align"     => "left",
				      "index"     => "type",
				      "type"      => "options",
				      "options"   => array( 0 => "Files and Database",1 => "Files only", 2 => "Database only",
				      ),
				  ));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_backupsuiteprofile', array(
					 'label'=> Mage::helper('backupsuite')->__('Remove Backupsuiteprofile'),
					 'url'  => $this->getUrl('*/adminhtml_backupsuiteprofile/massRemove'),
					 'confirm' => Mage::helper('backupsuite')->__('Are you sure?')
				));
			return $this;
		}
			

}