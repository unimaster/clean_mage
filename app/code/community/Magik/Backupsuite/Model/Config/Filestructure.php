<?php
class Magik_Backupsuite_Model_Config_Filestructure
{
    public function toOptionArray() {
		$options	= array();
		$baseDir	= Mage::getBaseDir();
		$handle		= opendir($baseDir);

		while (($folder = readdir($handle)) !== false) {
			if ($folder == '.' || $folder == '..') {

				continue;
			}
		 $cmd="du -sh ".$baseDir.'/'.$folder." |xargs | cut -d ' ' -f 1";
		$t=exec($cmd);	

			$options[]	= array(
				'value'		=> $folder,
				'label'		=> $folder.' [ '. $t .']',
			);

		}

		closedir($handle);

		return $options;
	}

} 
