<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Localfiles extends Varien_Data_Form_Element_Abstract{



   public function __construct($attributes=array())
    {
       parent::__construct($attributes);
    }

    public function getElementHtml(){

      if( Mage::registry("backupsuite_data") && Mage::registry("backupsuite_data")->getId() ){
    
	    $id=Mage::registry("backupsuite_data")->getId();
	    $Logdata = Mage::getModel("backupsuite/backupsuite")->load($id);
	    $filename=$Logdata->getFilename();
	    $dbfile=$Logdata->getDbfilename();
	    $logfile=$Logdata->getLogDetail();
	    
	    $html='<p>'.$filename.'</p>';
	    $html.='<p>'.$dbfile.'</p>';
	    $html.='<p>'.$logfile.'</p>';
	    return $html;

	}
   }
} 
