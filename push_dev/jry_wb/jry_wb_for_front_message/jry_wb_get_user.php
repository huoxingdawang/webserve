<?php
	include_once("../tools/jry_wb_includes.php");
	$conn=jry_wb_connect_database();
	$admin_mode=(($_GET['admin_mode']=='true')&&($jry_wb_login_user['id']!=-1)&&$jry_wb_login_user['compentence']['manageusers']&&$jry_wb_login_user['compentence']['manage']);	
	if($_GET['action']=='new')
	{
		$q='SELECT *,'.JRY_WB_DATABASE_GENERAL_PREFIX.'users.id AS id
			FROM '.JRY_WB_DATABASE_MANAGE_SYSTEM.'competence 
			INNER JOIN '.JRY_WB_DATABASE_GENERAL.'users  ON ('.JRY_WB_DATABASE_GENERAL_PREFIX.'users.type = '.JRY_WB_DATABASE_MANAGE_SYSTEM_PREFIX.'competence.type) 
			LEFT JOIN '.JRY_WB_DATABASE_GENERAL.'login  ON ('.JRY_WB_DATABASE_GENERAL_PREFIX.'users.id = '.JRY_WB_DATABASE_GENERAL_PREFIX."login.id)
			order by ".JRY_WB_DATABASE_GENERAL."users.id desc limit 1";
		$st = $conn->prepare($q);
		$st->execute();
		foreach($st->fetchAll()as $user);
	}
	else
	{
		$q='SELECT *,'.JRY_WB_DATABASE_GENERAL_PREFIX.'users.id AS id
			FROM '.JRY_WB_DATABASE_MANAGE_SYSTEM.'competence 
			INNER JOIN '.JRY_WB_DATABASE_GENERAL.'users  ON ('.JRY_WB_DATABASE_GENERAL_PREFIX.'users.type = '.JRY_WB_DATABASE_MANAGE_SYSTEM_PREFIX.'competence.type) 
			LEFT JOIN '.JRY_WB_DATABASE_GENERAL.'login  ON ('.JRY_WB_DATABASE_GENERAL_PREFIX.'users.id = '.JRY_WB_DATABASE_GENERAL_PREFIX."login.id)
			where ".JRY_WB_DATABASE_GENERAL_PREFIX."users.id =? LIMIT 1";
		$st = $conn->prepare($q);
		$st->bindParam(1,$_GET['id']);
		$st->execute();
		foreach($st->fetchAll()as $user);
	}
	if($user==null)
	{
		echo json_encode(array(	'id'=>(int)$_GET['id'],
								'use'=>1,
								'show'=>null,
								'name'=>null,
								'head'=>null,
								'ips'=>''
						));
		exit();			
	}
	if(!$user['use']&&!$jry_wb_login_user['manageusers'])
	{
		echo json_encode(array(	'id'=>(int)$_GET['id'],
								'use'=>(int)$user['use'],
								'ips'=>''
						));
		exit();		
	}
	if((strtotime($user['lasttime'])-strtotime(urldecode($_GET['lasttime'])))<=0)
	{
		echo json_encode(array('id'=>-1,'use'=>1));
		exit();
	}
	if($user['oauth_qq']!='')
		$user['oauth_qq']=json_decode($user['oauth_qq']);
	if($user['oauth_github']!='')
		$user['oauth_github']=json_decode($user['oauth_github']);	
	if($user['oauth_mi']!='')
		$user['oauth_mi']=json_decode($user['oauth_mi']);	
	if($user['oauth_gitee']!='')
		$user['oauth_gitee']=json_decode(preg_replace('/\\\n/i','<br>',$user['oauth_gitee']));
	$ip=array();
	if($user['ip_show']||($admin_mode))
	{
		$i=0;
		$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_GENERAL.'login where id=?');
		$st->bindParam(1,$user['id']);
		$st->execute();
		$user['ips']=$st->fetchAll();
		foreach($user['ips']as $ips)
		{
			$arr=jry_wb_get_ip_address($ips['ip']);
			if($arr->data->isp=='unknow')
				$ip[$i]='未知地区|'.$ips['time'].' '.jry_wb_get_device_from_database($ips['device']);
			else if($arr->data->isp=='内网IP')
				$ip[$i]='内网IP|'.$ips['time'].'|'.jry_wb_get_device_from_database($ips['device']);
			else
				$ip[$i] = $arr->data->country.$arr->data->region.$arr->data->city.$arr->data->isp.'|'.$ips['time'].'|'.jry_wb_get_device_from_database($ips['device']);
			$i++;
		}
	}
	if($user['mail']!=''&&(!$admin_mode))
	{
		if($user['mail_show']==0)
		{
			$buf=explode('@',$user['mail']);
			$user['mail']=substr_replace($buf[0],'****',3,count($buf[0])-3).'@'.$buf[1];
		}else if($user['mail_show']==1)
		{
			$buf=explode('@',$user['mail']);
			$count=count($buf[0]);
			$user['mail']='';
			for($i=0;$i<$count;$i++)
				$user['mail'].='*';
			$user['mail'].='@'.$buf[1];
		}
	}
	if($user['tel']!=''&&(!$admin_mode))
	{
		if($user['tel_show']==0)
			$user['tel']=substr_replace($user['tel'],'****',3,4);
		else if($user['tel_show']==1)
			$user['tel']=substr_replace($user['tel'],'***********',0,11);
	}
	if($_GET['action']=='new')
		$id=$user['id'];
	else
		$id=$_GET['id'];
	$user['head_special']=json_decode($user['head_special']);
	if($user['head_special']->mouse_on->times!=-1&&($user['head_special']->mouse_out->times==0||$user['head_special']->mouse_out->speed==0))
	{
		$user['head_special']->mouse_out->speed=$user['head_special']->mouse_on->speed;
		$user['head_special']->mouse_out->direction=(($user['head_special']->mouse_on->direction)?0:1);
		$user['head_special']->mouse_out->times=1;
	}
	$user['head_special']->mouse_out->result=jry_wb_get_user_head_style_out($user);
	$user['head_special']->mouse_on->result=jry_wb_get_user_head_style_on($user);
	if($user['head']==''||$user['head']==NULL||$user['head']=='NULL')
		if($user['sex']==0)
			$user['head']=array('type'=>'default_head_woman');
		else
			$user['head']=array('type'=>'default_head_man');
	else
		$user['head']=json_decode($user['head'],true);	
	$data=array('id'=>(int)$id,
				'head'=>$user['head'],
				'head_special'=>$user['head_special'],
				'green_money'=>$user['green_money'],
				'enroldate'=>$user['enroldate'],
				'competencename'=>$user['competencename'],
				'color'=>$user['color'],						
				'name'=>$user['name'],
				'sex'=>$user['sex'],
				'tel'=>$user['tel'],
				'mail'=>$user['mail'],
				'language'=>$user['language'],
				'zhushi'=>$user['zhushi'],
				'lasttime'=>$user['lasttime'],
				'lasttime_sync'=>jry_wb_get_time(),
				'type'=>$user['type'],
				'use'=>$user['use'],							
				'oauth_qq'=>(($admin_mode||$user['oauth_show'])?$user['oauth_qq']->message:null),
				'oauth_mi'=>(($admin_mode||$user['oauth_show'])?$user['oauth_mi']->message:null),
				'oauth_github'=>(($admin_mode||$user['oauth_show'])?$user['oauth_github']->message:null),
				'oauth_gitee'=>(($admin_mode||$user['oauth_show'])?$user['oauth_gitee']->message:null),
				'login_addr'=>($user['ip_show']||($admin_mode))?$ip:-1,
				'password'=>($admin_mode)?$user['password']:'',
				'extern'=>($admin_mode)?json_decode($user['extern']):''
				);
	echo json_encode($data);
?>