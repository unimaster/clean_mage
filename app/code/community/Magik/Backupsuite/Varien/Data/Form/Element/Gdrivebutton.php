<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Gdrivebutton extends Varien_Data_Form_Element_Abstract{

   public function __construct($attributes=array())
    {
       parent::__construct($attributes);
    }

    public function getElementHtml()
    {   
	$get_profileid=explode("_",$this->getData('id'));
	$checkAuthcode =Mage::getModel('backupsuite/backupsuiteprofile')->load($get_profileid[3])->getData('gdrive_authcode');
	$url =Mage::helper('adminhtml')->getUrl("backupsuite/adminhtml_backupsuite/gdrivevalid/pid/".$get_profileid[3]);
	$removeurl =Mage::helper('adminhtml')->getUrl("backupsuite/adminhtml_backupsuite/gdriveremove/pid/".$get_profileid[3]);

	$dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
	if(!is_writable($dirPath)) {
		$file = new Varien_Io_File();
		$dataDir = $file->mkdir($dirPath);
		$contentStr='Order deny,allow'. "\n";	
		$contentStr.='Allow from all'	;
		file_put_contents($dirPath."/.htaccess", $contentStr);
	}
	if(is_file($dirPath."/token_".$get_profileid[3].".json")){
	  $gdriveid='dropbox_oauth_remove_'.$get_profileid[3];
	    //$dropboxid='dropbox_oauth_remove';
	    $object=Mage::getSingleton('backupsuite/gdriveauth');

	    $html = '<script type="text/javascript">
			//<![CDATA[                        
			  function gdriveRemove() {     
			  new Ajax.Request("'.$removeurl.'", {
			  method:     "get",
			  onSuccess: function(transport){
			  	document.location.reload();
		      }
		      });
	  
		    }
		  //]]>
		  </script>'; 
	  
	  $onclick="javascript:gdriveRemove(); return false;";
	  $html.='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Authorize" id="'.$gdriveid.'">
	  <span><span><span>Remove account of "'.$object->printAbout($get_profileid[3]).'"</span></span></span></button>';
	  return $html;

	}else if($checkAuthcode != ''){
	    //Mage::log($url); //To check if URL is correct (and it is correct)
	    $gdriveid='gdrive_oauth_button_'.$get_profileid[3];
	    $html = '<script type="text/javascript">
			//<![CDATA[                        
			  function gDrivecheck() {     
			  new Ajax.Request("'.$url.'", {
			  method:     "get",
			  onSuccess: function(transport){
			  	document.location.reload();
		      }
		      });
	  
		    }
		  //]]>
		  </script>'; 
	  
	  $onclick="javascript:gDrivecheck();return false;";
	  $html.='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Authorize" id="'.$gdriveid.'">
	  <span><span><span>Complete Authorization</span></span></span></button>';
	  return $html;
	}else{
	  $gdriveid='gdrive_oauth_button_'.$get_profileid[3];
	  //Mage::log($url); //To check if URL is correct (and it is correct)
	    $onclick="javascript:window.open('".$url."')";
	    $html='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Authorize" id="'.$gdriveid.'">
	    <span><span><span>Authorize</span></span></span></button>';
	    return $html;

        }
	
    

	
  
       
                 
  
    }// function close
} 
