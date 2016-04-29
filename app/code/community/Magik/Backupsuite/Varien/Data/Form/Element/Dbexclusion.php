<?php
class Magik_Backupsuite_Varien_Data_Form_Element_Dbexclusion extends Varien_Data_Form_Element_Abstract{

    public function __construct($attributes=array()) {
       parent::__construct($attributes);
    }

    public function getElementHtml() {

      if( Mage::registry("backupsuiteprofile_data") && Mage::registry("backupsuiteprofile_data")->getId() ){

	      $id=Mage::registry("backupsuiteprofile_data")->getId();
	      $profileData = Mage::getModel("backupsuite/backupsuiteprofile")->load($id);
	      $tablesExclusion=$profileData->getDbexclusion();
	      $exclusionArray=explode(",",$tablesExclusion); 
	      $html='';
	      $tableurl = Mage::helper('adminhtml')->getUrl("adminhtml/backupsuite_backupsuite/caltablespace");
	      $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
	      $sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname'";
	      $collection = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);

	      foreach ($collection as $db) {
		$reqTablename=$db['TABLE_NAME'];
		
		  $html.= '<dir class="mgkrow">
			   <input type="checkbox" name="dbexclusion[]" value="'.$reqTablename.'" '.(( in_array($reqTablename, $exclusionArray)) ? 'checked':"").'>';
		  $html.= '&nbsp;&nbsp;'.$reqTablename;
		  $onclick="caltableSpace('$reqTablename')";
		  $html.='<span class="tableSpaceButton" id="'.$reqTablename.'" onclick="'.$onclick.'"> [Calculate Disk Space]</span>';
		  $html.='</dir>';
	      }
      
	      $html .= '<script type="text/javascript">
				//<![CDATA[   
			      function caltableSpace(tablepath) {    
				  var param = {path: tablepath}; 
				  new Ajax.Request("'.$tableurl.'", {
				  parameters: param,
				  method:     "post",
				  onSuccess: function(transport){
					    if ($("sizeOf"+tablepath)){
						$("sizeOf"+tablepath).remove();
					    }		    
					    var tablecount = document.createElement("span");  
					    tablecount.id = "sizeOf"+tablepath;
					    tablecount.className = "tableSize"
					    tablecount.appendChild(document.createTextNode("\t\t["+transport.responseText+"]"));                                
					    $(tablepath).insert({"after" : tablecount});  	
				  }
			      });
		  
			    }
			  //]]>
			  </script>'; 
	      return $html;

    }else{
	      $html='';
	      $tableurl = Mage::helper('adminhtml')->getUrl("adminhtml/backupsuite_backupsuite/caltablespace");
	      $dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
	      $sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname'";
	      $collection = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
	    
	      foreach ($collection as $db) {
		$reqTablename=$db['TABLE_NAME'];
		
		  $html.= '<dir class="mgkrow">
			   <input type="checkbox" name="dbexclusion[]" value="'.$reqTablename.'">';
		  $html.= '&nbsp;&nbsp;'.$reqTablename;
		  $onclick="caltableSpace('$reqTablename')";
		  $html.='<span class="tableSpaceButton" id="'.$reqTablename.'" onclick="'.$onclick.'"> [Calculate Disk Space]</span>';
		  $html.='</dir>';
	      }
      
	      $html .= '<script type="text/javascript">
				//<![CDATA[   
			      function caltableSpace(tablepath) {    
				  var param = {path: tablepath}; 
				  new Ajax.Request("'.$tableurl.'", {
				  parameters: param,
				  method:     "post",
				  onSuccess: function(transport){
					    if ($("sizeOf"+tablepath)){
						$("sizeOf"+tablepath).remove();
					    }		    
					    var tablecount = document.createElement("span");  
					    tablecount.id = "sizeOf"+tablepath;
					    tablecount.className = "tableSize"
					    tablecount.appendChild(document.createTextNode("\t\t["+transport.responseText+"]"));                                
					    $(tablepath).insert({"after" : tablecount});  	
				  }
			      });
		  
			    }
			  //]]>
			  </script>'; 
	      return $html;
	      
	}
	
   }//function
} 
 
