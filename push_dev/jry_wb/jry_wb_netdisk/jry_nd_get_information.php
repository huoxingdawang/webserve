<?php
	include_once("../jry_wb_tools/jry_wb_includes.php");
	include_once("../jry_wb_configs/jry_wb_config_netdisk.php");
	include_once("jry_wb_nd_tools.php");
	$action=$_GET['action'];
	jry_wb_get_netdisk_information($conn);
	if(($file=fopen('jry_nd.fast_save_message','r'))==false)
	{
		$st = $conn->prepare('SELECT lasttime FROM '.JRY_WB_DATABASE_NETDISK.'area ORDER BY lasttime DESC LIMIT 1;');	$st->execute();		$data['area']=$st->fetchAll()[0]['lasttime'];
		$st = $conn->prepare('SELECT lasttime FROM '.JRY_WB_DATABASE_NETDISK.'group ORDER BY lasttime DESC LIMIT 1;');	$st->execute();		$data['group']=$st->fetchAll()[0]['lasttime'];
		$file=fopen('jry_nd.fast_save_message','w');
		fwrite($file,json_encode($data));
		fclose($file);
		$data['new']=true;
	}
	else
	{
		$data=json_decode(fread($file,filesize('jry_nd.fast_save_message')));
		fclose($file);
	}
	if($action=='area')
	{
		$ans=[];
		if((urldecode($_GET['lasttime']))<=($data->area))
		{
			$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_NETDISK.'area WHERE lasttime>?;');
			$st->bindParam(1,urldecode($_GET['lasttime']));
			$st->execute();
			foreach($st->fetchAll() as $one)
				$ans[]=array(	'area_id'=>$one['area_id'],
								'id'=>$one['id'],
								'name'=>$one['name'],
								'fast'=>$one['fast'],
								'type'=>$one['type'],
								'lasttime'=>$one['lasttime']);
		}
		echo json_encode(array('code'=>true,'data'=>$ans));
		exit();
	}
	if($action=='group')
	{
		$ans=[];
		if((urldecode($_GET['lasttime']))<=($data->group))
		{
			$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_NETDISK.'group WHERE lasttime>?;');
			$st->bindParam(1,urldecode($_GET['lasttime']));
			$st->execute();
			foreach($st->fetchAll() as $one)
				$ans[]=array(	'group_id'=>$one['group_id'],
								'group_name'=>$one['group_name'],
								'jry_nd_group_type'=>$one['jry_nd_group_type'],
								'lasttime'=>$one['lasttime']);
		}
		echo json_encode(array('code'=>true,'data'=>$ans));
		exit();
	}
	try{jry_wb_check_compentence(NULL,array('use','usenetdisk'));}catch(jry_wb_exception $e){echo $e->getMessage();exit();}	
	if($action=='file_list')
	{
		$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_NETDISK.'file_list WHERE lasttime>? AND id=?;');
		$st->bindValue(1,date('Y-m-d H:i:s',strtotime(urldecode($_GET['lasttime']))));
		$st->bindValue(2,$jry_wb_login_user['id']);
		$st->execute();
		$ans=[];
		$data=$st->fetchAll();
		$n=count($data);
		for($i=0;$i<$n;$i++)
			$ans[$i]=array(	'file_id'=>$data[$i]['file_id'],
							'id'=>$data[$i]['id'],
							'father'=>$data[$i]['father'],
							'name'=>$data[$i]['name'],
							'type'=>$data[$i]['type'],
							'area'=>$data[$i]['area'],
							'size'=>$data[$i]['size'],
							'download_times'=>$data[$i]['download_times'],
							'uploading'=>$data[$i]['uploading'],
							'trust'=>$data[$i]['trust'],
							'toll_flow'=>$data[$i]['toll_flow'],
							'delete'=>$data[$i]['delete'],
							'isdir'=>$data[$i]['isdir'],
							'share'=>$data[$i]['share'],
							'self_share'=>$data[$i]['self_share'],
							'share_list'=>json_decode($data[$i]['share_list']),
							'lasttime'=>$data[$i]['lasttime']);
		echo json_encode(array('code'=>true,'data'=>$ans));		
		exit();
	}
	if($action=='share')
	{
		$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_NETDISK.'share WHERE id=? AND file_id=?;');
		$st->bindValue(1,$jry_wb_login_user['id']);
		$st->bindValue(2,$_POST['file_id']);
		$st->execute();
		$ans=[];
		foreach($st->fetchAll() as $data)
			$ans[]=array(	'file_id'=>$data['file_id'],
							'share_id'=>$data['share_id'],
							'key'=>$data['key'],
							'fastdownload'=>$data['fastdownload'],
							'requesturl'=>$data['requesturl'],
							'lasttime'=>$data['lasttime']);
		echo json_encode(array('code'=>true,'data'=>$ans));		
		exit();
	}
	if($action=='share_list')
	{
		$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_NETDISK.'share WHERE id=?;');
		$st->bindValue(1,$jry_wb_login_user['id']);
		$st->execute();
		$ans=[];
		foreach($st->fetchAll() as $data)
			$ans[]=array(	'file_id'=>$data['file_id'],
							'share_id'=>$data['share_id'],
							'key'=>$data['key'],
							'fastdownload'=>$data['fastdownload'],
							'requesturl'=>$data['requesturl'],
							'lasttime'=>$data['lasttime']);
		echo json_encode(array('code'=>true,'data'=>$ans));		
		exit();
	}	
?>