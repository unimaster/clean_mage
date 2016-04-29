<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Boxbutton extends Varien_Data_Form_Element_Abstract{

   public function __construct($attributes=array())
    {
       parent::__construct($attributes);
    }

    public function getElementHtml()
    {   
	$get_profileid=explode("_",$this->getData('id'));
	
	$url = Mage::helper('adminhtml')->getUrl("backupsuite/adminhtml_backupsuite/boxvalid/pid/".$get_profileid[3]);
        $removeurl = Mage::helper('adminhtml')->getUrl("backupsuite/adminhtml_backupsuite/removeboxuser/pid/".$get_profileid[3]);
	
	
        $dirPath = Mage::getBaseDir().DS.'var'.DS.'data';
	if(!is_writable($dirPath)) {
		$file = new Varien_Io_File();
		$dataDir = $file->mkdir($dirPath);
		$contentStr='Order deny,allow'. "\n";	
		$contentStr.='Allow from all'	;
		file_put_contents($dirPath."/.htaccess", $contentStr);
	}
	if(is_file($dirPath."/token_".$get_profileid[3].".box")){ 
	  $boxid='box_oauth_remove_'.$get_profileid[3];
	 $userData=Mage::getModel('backupsuite/boxnet')->getBoxAccountDetails($get_profileid[3]);
	$uname=$userData['name'];
	  $html = '<script type="text/javascript">
			//<![CDATA[                        
			  function boxcheck() {     
			  new Ajax.Request("'.$removeurl.'", {
			  method:     "get",
			  onSuccess: function(transport){
			  	document.location.reload();
		      }
		      });
	  
		    }
		  //]]>
		  </script>'; 
	  
	  $onclick="javascript:boxcheck();return false;";
	  $html.='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Remove account" id="'.$boxid.'">
	  <span><span><span>Remove account of "'.$uname.'"</span></span></span></button>';
	  return $html;
    

	}else{
	    $boxid='box_oauth_button_'.$get_profileid[3];
	    $html = '<script type="text/javascript">
		      //<![CDATA[                        
			function boxcheck() {     
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
	$html.='<button style="" onclick="'.$onclick.'" class="scalable"  type="button" title="Authorize" id="box_oauth_button">
	<span><span><span>Authorize</span></span></span></button>';
	return $html;
	}
  
       
                 
  
    }
} 
