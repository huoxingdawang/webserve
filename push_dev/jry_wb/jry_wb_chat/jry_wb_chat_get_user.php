<?php
	include_once("jry_wb_chat_includes.php");
	function jry_wb_chat_get_user($conn,&$user,$new=false)
	{
		jry_wb_check_compentence($user,['usechat'],NULL);
		if($user['ch_ei']!=NULL)
			return $user['ch_ei'];
		$st = $conn->prepare('SELECT * FROM '.JRY_WB_DATABASE_CHAT.'users WHERE id=? LIMIT 1');
		$st->bindValue(1,$user['id']);
		$st->execute();
		$data=$st->fetchAll();
		if(count($data)==0)
		{
			if($new!==true)
				return null;
			$st = $conn->prepare('INSERT INTO '.JRY_WB_DATABASE_CHAT.'users (id,lasttime) VALUES (?,?)');
			$st->bindValue(1,$user['id']);
			$st->bindValue(2,jry_wb_get_time());
			$st->execute();
			return $user['ch_ei']=array('id'=>$user['id'],'say_count'=>0,'chat_rooms'=>array(),'lasttime'=>jry_wb_get_time());
		}
		if($data[0]['chat_rooms']==NULL)
			$data[0]['chat_rooms']=array();
		else
			$data[0]['chat_rooms']=json_decode($data[0]['chat_rooms']);
		return $user['ch_ei']=$data[0];
	}
