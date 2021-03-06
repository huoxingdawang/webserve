<?php 
	include_once("../jry_wb_tools/jry_wb_includes.php");
	$st =jry_wb_connect_database()->prepare("DELETE FROM ".JRY_WB_DATABASE_BLOG."text where lasttime<? AND `delete` =1");
	$st->bindParam(1,date("Y-m-d H;i:s",time()-JRY_WB_LOGIN_TIME));
	$st->execute();		
	$action=$_GET['action'];
	if($action=='get_blog_list')
	{
		$conn=jry_wb_connect_database();
		$q ="SELECT * FROM ".JRY_WB_DATABASE_BLOG."text where lasttime>? ORDER BY lasttime DESC"; 
		$st = $conn->prepare($q);
		$st->bindParam(1,urldecode($_GET['lasttime']));
		$st->execute();				
		$data=$st->fetchAll();
		$total=count($data);
		$json=array();		
		for($i=0;$i<$total;$i++)
		{
			if($data[$i]['ifshow'])
			{
				$json[$i]=	array(	'blog_id'=>$data[$i]['blog_id'],
									'title'=>$data[$i]['title'],
									'lasttime'=>$data[$i]['lasttime'],
									'last_modify_time'=>$data[$i]['last_modify_time'],
									'last_read_time'=>$data[$i]['last_read_time'],
									'show'=>$data[$i]['ifshow'],
									'delete'=>$data[$i]['delete'],
									'id'=>$data[$i]['id']
									);
			}
			else
			{
				$json[$i]=	array(	'blog_id'=>$data[$i]['blog_id'],
									'lasttime'=>$data[$i]['lasttime'],
									'delete'=>$data[$i]['delete'],
									'show'=>$data[$i]['ifshow']
									);				
			}
		}
		echo json_encode(array('code'=>true,'data'=>$json));	
		exit();
	} 
	if($action=='get_blog_one')
	{
		$conn=jry_wb_connect_database();
		$st = $conn->prepare("SELECT * FROM ".JRY_WB_DATABASE_BLOG."text where blog_id=?");
		$st->bindParam(1,$_GET['blog_id']);
		$st->execute();			
		foreach($st->fetchAll() as $data);
		if($data['ifshow'])
		{
			echo json_encode(array(	'blog_id'=>$data['blog_id'],
									'data'=>json_decode($data['data']),
									'lasttime'=>$data['lasttime'],
									'last_modify_time'=>$data['last_modify_time'],
									'last_read_time'=>$data['last_read_time'],
									'ifshow'=>$data['ifshow'],
									'delete'=>$data['delete'],
									'id'=>$data['id']
									));
			$st = $conn->prepare('INSERT INTO '.JRY_WB_DATABASE_LOG.'blog_reading (`id`,`blog_id`,`time`,`ip`,`device`,`browser`) VALUES (?,?,?,?,?,?);');
			$st->bindValue(1,$jry_wb_login_user['id']);
			$st->bindValue(2,$_GET['blog_id']);
			$st->bindValue(3,jry_wb_get_time());
			$st->bindValue(4,$_SERVER['REMOTE_ADDR']);
			$st->bindValue(5,jry_wb_get_device(true));
			$st->bindValue(6,jry_wb_get_browser(true));
			$st->execute();
			$st = $conn->prepare("UPDATE ".JRY_WB_DATABASE_BLOG."text SET readingcount = readingcount+1 ,lasttime=?,last_read_time=? where blog_id = ?");
			$st->bindParam(1,jry_wb_get_time());
			$st->bindParam(2,jry_wb_get_time());
			$st->bindParam(3,intval($_GET['blog_id']));
			$st->execute();			
		}
		else
		{
			header('HTTP/1.1 404 Not Found'); 
			header("status: 404 Not Found"); 
			include('../../404.php');
		}			
		exit();
	}
	try{jry_wb_check_compentence();}catch(jry_wb_exception $e){echo $e->getMessage();exit();}
	if($action=='get_draft_list')
	{
		$conn=jry_wb_connect_database();
		$q ="SELECT * FROM ".JRY_WB_DATABASE_BLOG."text where id=? AND lasttime>? ORDER BY lasttime DESC"; 
		$st = $conn->prepare($q);
		$st->bindParam(1,$jry_wb_login_user['id']);
		$st->bindParam(2,urldecode($_GET['lasttime']));
		$st->execute();				
		$data=$st->fetchAll();
		$total=count($data);
		$json=array();
		for($i=0;$i<$total;$i++)
		{
			$json[$i]=	array(	'blog_id'=>$data[$i]['blog_id'],
								'title'=>$data[$i]['title'],
								'last_modify_time'=>$data[$i]['last_modify_time'],
								'last_read_time'=>$data[$i]['last_read_time'],
								'lasttime'=>$data[$i]['lasttime'],
								'delete'=>$data[$i]['delete'],
								'show'=>$data[$i]['ifshow']
								);
		}
		echo json_encode(array('code'=>true,'data'=>$json));	
		exit();
	} 	
	if($action=='get_draft_one')
	{
		$conn=jry_wb_connect_database();
		$q ="SELECT * FROM ".JRY_WB_DATABASE_BLOG."text where id=? AND blog_id=? ORDER BY lasttime DESC";
		$st = $conn->prepare($q);
		$st->bindParam(1,$jry_wb_login_user['id']);
		$st->bindParam(2,$_GET['blog_id']);
		$st->execute();			
		foreach($st->fetchAll() as $data);		
		echo json_encode(array(	'blog_id'=>$data['blog_id'],
								'title'=>$data['title'],
								'delete'=>$data['delete'],
								'data'=>json_decode($data['data']),
								'last_modify_time'=>$data['last_modify_time'],
								'last_read_time'=>$data['last_read_time'],
								'lasttime'=>$data['lasttime'],
								'show'=>$data['ifshow']
								));			
	}
?>