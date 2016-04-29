<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Dropboxbutton extends Varien_Data_Form_Element_Abstract{

   public function __construct($attributes=array())
    {
       parent::__construct($attributes);
    }

    public function getElementHtml()
    {   
	$get_profileid=explode("_",$this->getData('id'));
	$url = Mage::helper('adminhtml')->getUrl("backupsuite/adminhtml_backupsuite/check/pid/".$get_profileid[3]);
        $removeurl = Mage::helper('adminhtml')->getUrl("backupsuite/adminhtml_backupsuite/removedropboxuser/pid/".$get_profileid[3]);
	
	
        $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
	if(!is_writable($dirPath)) {
		$file = new Varien_Io_File();
		$dataDir = $file->mkdir($dirPath);
		$contentStr='Order deny,allow'. "\n";	
		$contentStr.='Allow from all'	;
		file_put_contents($dirPath."/.htaccess", $contentStr);
	}
	if(is_file($dirPath."/access_".$get_profileid[3].".token")){
	  $dropboxid='dropbox_oauth_remove_'.$get_profileid[3];
	  $userData=Mage::getModel('backupsuite/dropbox')->getAccountDetails($get_profileid[3]);
	  $html = '<script type="text/javascript">
			//<![CDATA[                        
			  function check() {     
			  new Ajax.Request("'.$removeurl.'", {
			  method:     "get",
			  onSuccess: function(transport){
			  	document.location.reload();
		      }
		      });
	  
		    }
		  //]]>
		  </script>'; 
	  
	  $onclick="javascript:check(); return false;";
	  $html.='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Authorize" id="'.$dropboxid.'">
	  <span><span><span>Remove account of "'.$userData->display_name.'"</span></span></span></button>';
	  return $html;
	}else{
	    $dropboxid='dropbox_oauth_button_'.$get_profileid[3];
	    $html = '<script type="text/javascript">
		      //<![CDATA[                        
			function check() {     
			new Ajax.Request("'.$removeurl.'", {
			method:     "get",
			onSuccess: function(transport){
			document.location.reload();		
		    }
		    });
        
		  }
                //]]>
                </script>'; 
	
	$onclick="javascript:window.open('".$url."')";
	$html.='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Authorize" id="dropbox_oauth_button">
	<span><span><span>Authorize</span></span></span></button>';
	return $html;
	}
    }
} 
