<?php
class Magik_Backupsuite_Adminhtml_Backupsuite_BackupsuiteprofileController extends Mage_Adminhtml_Controller_Action {

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("backupsuite/backupsuiteprofile")->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuiteprofile  Manager"),Mage::helper("adminhtml")->__("Backupsuiteprofile Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Backupsuite"));
			    $this->_title($this->__("Manager Backupsuiteprofile"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Backupsuite"));
				$this->_title($this->__("Backupsuiteprofile"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("backupsuite/backupsuiteprofile")->load($id);
				if ($model->getId()) {
					Mage::register("backupsuiteprofile_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("backupsuite/backupsuiteprofile");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuiteprofile Manager"), Mage::helper("adminhtml")->__("Backupsuiteprofile Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuiteprofile Description"), Mage::helper("adminhtml")->__("Backupsuiteprofile Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit"))->_addLeft($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("backupsuite")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Backupsuite"));
		$this->_title($this->__("Backupsuiteprofile"));
		$this->_title($this->__("New Item"));

		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("backupsuite/backupsuiteprofile")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("backupsuiteprofile_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("backupsuite/backupsuiteprofile");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuiteprofile Manager"), Mage::helper("adminhtml")->__("Backupsuiteprofile Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backupsuiteprofile Description"), Mage::helper("adminhtml")->__("Backupsuiteprofile Description"));


		$this->_addContent($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit"))->_addLeft($this->getLayout()->createBlock("backupsuite/adminhtml_backupsuiteprofile_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {
					      if(count($post_data['filesexclusion']) > 0){
								for($j=0;$j<=count($post_data['filesexclusion']);$j++){
								  $exclusive_files=implode(",",$post_data['filesexclusion']);
								}
								$post_data['filesexclusion']=$exclusive_files;
					      }else{$post_data['filesexclusion']='';}

					      if(count($post_data['dbexclusion']) > 0){
							  for($k=0;$k<=count($post_data['dbexclusion']);$k++){
							    $exclusive_tables=implode(",",$post_data['dbexclusion']);
							   }
							  $post_data['dbexclusion']=$exclusive_tables;
						}else{$post_data['dbexclusion']='';}
						
				


						$model = Mage::getModel("backupsuite/backupsuiteprofile")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();
						
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Profile was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setBackupsuiteprofileData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setBackupsuiteprofileData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("backupsuite/backupsuiteprofile");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("backupsuite/backupsuiteprofile");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'backupsuiteprofile.csv';
			$grid       = $this->getLayout()->createBlock('backupsuite/adminhtml_backupsuiteprofile_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'backupsuiteprofile.xml';
			$grid       = $this->getLayout()->createBlock('backupsuite/adminhtml_backupsuiteprofile_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
