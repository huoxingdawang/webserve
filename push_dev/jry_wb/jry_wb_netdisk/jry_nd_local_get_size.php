<?php
	include_once("jry_wb_local_include.php");
	function jry_nd_local_get_size($area,$file)
	{
		if($area['type']!=0)
			throw new jry_wb_exception(json_encode(array('code'=>false,'reason'=>200000,'file'=>__FILE__,'line'=>__LINE__)));		
		return filesize($area['config_message']->dir.JRY_ND_UPLOAD_FILE_PREFIX.$file['file_id'].'_jryupload');
	}
?>