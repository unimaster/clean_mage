<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Filesexclusion extends Varien_Data_Form_Element_Abstract{

    public function __construct($attributes=array()) {
       parent::__construct($attributes);
    }

    public function getElementHtml() {
      if( Mage::registry("backupsuiteprofile_data") && Mage::registry("backupsuiteprofile_data")->getId() ){

	      $id=Mage::registry("backupsuiteprofile_data")->getId();
	      $profileData = Mage::getModel("backupsuite/backupsuiteprofile")->load($id);
	      $filesExclusion=$profileData->getFilesexclusion();
	      $exclusionArray=explode(",",$filesExclusion); 
   
	      $html='';
	      $options	= array();
	      $baseDir	= Mage::getBaseDir();
	      $handle	= opendir($baseDir);
	      $spaceurl = Mage::helper('adminhtml')->getUrl("adminhtml/backupsuite_backupsuite/calspace");

	      while (($folder = readdir($handle)) !== false) {
			if ($folder == '.' || $folder == '..') {
				continue;
			}

			$html.= '<dir class="mgkrow">
				<input type="checkbox" name="filesexclusion[]" value="'.$folder.'" '.(( in_array($folder, $exclusionArray)) ? 'checked':"").'>';
			if(is_dir($folder)){
				      $html.='&nbsp;&nbsp;<a href="#">'. $folder .'</a>';
			}else{  $html.= '&nbsp;&nbsp;'.$folder; }

		      $onclick="calculateSpace('$folder')";
		      $html.='<span class="fileSpaceButton" id="'.$folder.'" onclick="'.$onclick.'"> [Calculate Disk Space]</span>';
			$html.='</dir>';
	      }
	      closedir($handle);

	      $html .= '<script type="text/javascript">
				//<![CDATA[   
			      function calculateSpace(exactpath) {    
				  var param = {path: exactpath}; 
				  new Ajax.Request("'.$spaceurl.'", {
				  parameters: param,
				  method:     "post",
				  onSuccess: function(transport){
					    if ($("sizeOf"+exactpath)){
						$("sizeOf"+exactpath).remove();
					    }		    
					    var spacecount = document.createElement("span");  
					    spacecount.id = "sizeOf"+exactpath;
					    spacecount.className = "fileSize"
					    spacecount.appendChild(document.createTextNode("\t\t["+transport.responseText+"]"));                                
					    $(exactpath).insert({"after" : spacecount});  	
				  }
			      });
		  
			    }
			  //]]>
			  </script>'; 
	      return $html;

    }else{

	      $html='';
	      $options	= array();
	      $baseDir	= Mage::getBaseDir();
	      $handle   = opendir($baseDir);
 $spaceurl = Mage::helper('adminhtml')->getUrl("adminhtml/backupsuite_backupsuite/calspace");
	      while (($folder = readdir($handle)) !== false) {
			if ($folder == '.' || $folder == '..') {
				continue;
			}
		      $html.= '<dir class="mgkrow"><input type="checkbox" name="filesexclusion[]" value="'.$folder.'">';
		      if(is_dir($folder)){
				$html.='&nbsp;&nbsp;<a href="#">'.$folder.'</a>';
		      }else{ $html.='&nbsp;&nbsp;'.$folder; }
		      $onclick="calculateSpace('$folder')";
		      $html.='<span class="fileSpaceButton" id="'.$folder.'" onclick="'.$onclick.'"> [Calculate Disk Space]</span>';
		      $html.='</dir>';

	      }
	      closedir($handle);
	      $html .= '<script type="text/javascript">
				//<![CDATA[   
			      function calculateSpace(exactpath) {    
				  var param = {path: exactpath}; 
				  new Ajax.Request("'.$spaceurl.'", {
				  parameters: param,
				  method:     "post",
				  onSuccess: function(transport){
					    if ($("sizeOf"+exactpath)){
						$("sizeOf"+exactpath).remove();
					    }		    
					    var spacecount = document.createElement("span");  
					    spacecount.id = "sizeOf"+exactpath;
					    spacecount.className = "fileSize"
					    spacecount.appendChild(document.createTextNode("\t\t["+transport.responseText+"]"));                                
					    $(exactpath).insert({"after" : spacecount});  	
				  }
			      });
		  
			    }
			  //]]>
			  </script>'; 
	      return $html;
	}
	
   }//function
} 
 
