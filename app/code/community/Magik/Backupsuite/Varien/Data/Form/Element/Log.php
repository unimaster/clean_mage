<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Log extends Varien_Data_Form_Element_Abstract{

    public function __construct($attributes=array()) {
       parent::__construct($attributes);
    }

    public function getElementHtml() {

	if( Mage::registry("backupsuite_data") && Mage::registry("backupsuite_data")->getId() ){
      
	      $id=Mage::registry("backupsuite_data")->getId();
	      $Logdata = Mage::getModel("backupsuite/backupsuite")->load($id);
	      $logfiles=$Logdata->getLogDetail();
	    
	      $defaultpath =  Mage::getBaseDir().DS.'var'.DS.'log';
	      $filepath= $defaultpath.DS.$logfiles;
	      $filecontent = '';
	      foreach (file($filepath) as $line) {
		  $parts = explode("\n", $line);
		  $filecontent .=$parts[0]."\r\n";
	      }
	      $html='<textarea name="content" id="content" disabled="disabled" rows="2" cols="40" style="width:900px">'.$filecontent.'</textarea>';
	    
	      return $html;

	}
   }
} 
