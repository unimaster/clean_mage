<?php

class Magik_Backupsuite_Block_Adminhtml_Backupsuite_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("backupsuiteGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("backupsuite/backupsuite")->getCollection();
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
				  'header' => Mage::helper("backupsuite")->__('Name'),
				  'align' => 'left',
				  'index' => 'name',
				));
			      
			      $this->addColumn('description', array(
				  'header' => Mage::helper("backupsuite")->__('Description'),
				  'align' => 'left',
				  'index' => 'description',
				));
			      $this->addColumn('profilename',array(
				'header'=> Mage::helper('backupsuite')->__('Profile Name'),
				'index' => 'profilename',
				'renderer' => 'backupsuite/adminhtml_backupsuite_renderer_profilename',
				));
			      $this->addColumn("by_cron", array(
				      "header"    => Mage::helper("backupsuite")->__("Cron"),
				      "align"     => "left",
				      "width"     => "80px",
				      "index"     => "by_cron",
				      "type"      => "options",
				      "options"   => array(
					  0 => "No",
					  1 => "Yes",
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
			$this->getMassactionBlock()->addItem('remove_backupsuite', array(
					 'label'=> Mage::helper('backupsuite')->__('Remove Backupsuite'),
					 'url'  => $this->getUrl('*/adminhtml_backupsuite/massRemove'),
					 'confirm' => Mage::helper('backupsuite')->__('Are you sure?')
				));
			return $this;
		}
			

}