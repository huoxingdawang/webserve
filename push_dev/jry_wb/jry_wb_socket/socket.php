<?php
	include_once("jry_wb_cli_include.php");
	if(constant('jry_wb_socket_switch')!==true)
	{
		echo jry_wb_php_cli_color('Failed!','light_red').' Please set '.jry_wb_php_cli_color('jry_wb_socket_switch','cyan').' to '.jry_wb_php_cli_color('true','green')."\n";
		exit();
	}
	ob_implicit_flush();
	$master=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	if($master===FALSE)
	{
		echo jry_wb_php_cli_color('Failed!','light_red').' On '.jry_wb_php_cli_color('socket_create()','cyan').' At FILE:'.jry_wb_php_cli_color(__FILE__,'yellow').' LINE:'.jry_wb_php_cli_color(__LINE__,'yellow').' Because '.socket_strerror(socket_last_error())."\n";
		exit();
	}
	socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, 1);
	$bind=socket_bind($master,constant('jry_wb_socket_host'),constant('jry_wb_socket_port')."\n");
	if($bind===FALSE)
	{
		echo jry_wb_php_cli_color('Failed!','light_red').' On '.jry_wb_php_cli_color('socket_bind()','cyan').' At FILE:'.jry_wb_php_cli_color(__FILE__,'yellow').' LINE:'.jry_wb_php_cli_color(__LINE__,'yellow').' Because '.socket_strerror(socket_last_error())."\n";
		exit();
	}
	$listen=socket_listen($master,constant('jry_wb_socket_max_client'));
	if($listen===FALSE)
	{
		echo jry_wb_php_cli_color('Failed!','light_red').' On '.jry_wb_php_cli_color('socket_listen()','cyan').' At FILE:'.jry_wb_php_cli_color(__FILE__,'yellow').' LINE:'.jry_wb_php_cli_color(__LINE__,'yellow').' Because '.socket_strerror(socket_last_error())."\n";
		exit();
	}
	$clients=array();
	$users=array();
	$c_to_u=array();
	$users_id=array_column($users,'id');	
	echo ("\n".jry_wb_php_cli_color('OK','green')."\nat ".jry_wb_php_cli_color(constant('jry_wb_socket_host').':'.constant('jry_wb_socket_port'),'cyan')."\nby ".jry_wb_php_cli_color('juruoyun web system '.constant('jry_wb_version'),'light_green')."\n");
	while(1)
	{
		$sockets=$clients;
		$sockets[]=$master;
		$write=NULL;
		$except=NULL;
		$tv_sec=NULL;
		socket_select($sockets, $write, $except, $tv_sec);
		//循环有状态变化的socket
		foreach ($sockets as $socket)
		{
			if($socket===$master)
			{
				$client = socket_accept($master);
				if ($client === FALSE)
					echo jry_wb_php_cli_color('Failed!','light_red').' On '.jry_wb_php_cli_color('socket_accept()','cyan').' At FILE:'.jry_wb_php_cli_color(__FILE__,'yellow').' LINE:'.jry_wb_php_cli_color(__LINE__,'yellow').' Because '.socket_strerror(socket_last_error())."\n";
				else
				{
					$header = socket_read($client, 1024);
					preg_match("/User-Agent: (.*)\r\n/", $header,$user_agent);
					$user_agent=$user_agent[1];
					preg_match("/Cookie: (.*)\r\n/", $header,$buf);
					$buf=explode(";",$buf[1]);
					$cookie=array();
					foreach($buf as $onecookie)
					{
						$buf2=explode("=",$onecookie);
						$cookie[str_replace(' ','',$buf2[0])]=str_replace(' ','',$buf2[1]);
					}
					socket_getpeername($client,$ip);					
					jry_wb_pretreatment($user,$cookie,$ip,$user_agent);
					if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $header, $match))//冒号后面有个空格
					{
						$secKey = $match[1];
						$secAccept = base64_encode(sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', TRUE));//握手算法固定的
						$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
						"Upgrade: websocket\r\n" .
						"Connection: Upgrade\r\n" .
						"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
						socket_write($client, $upgrade, strlen($upgrade));
					}
					if($user['id']==-1)
					{
						echo 'Not login user at '.jry_wb_php_cli_color($ip."\t".jry_wb_get_ip_address_string($ip),'cyan')."\n";
						jry_wb_socket_send($client,(array('code'=>false,'reason'=>100000)));
						socket_close($client);
						continue;
					}
					$result=array_search($user['id'],$users_id);
					if($result!==false&&($users[$result]['count']>(constant('jry_wb_socket_max_client_per_user')-1)))
					{
						echo jry_wb_php_cli_color($user['id'].'-'.$user['name'],'light_blue')."\t".jry_wb_php_cli_color('to much','red').' at '.jry_wb_php_cli_color($ip."\t".jry_wb_get_ip_address_string($ip),'cyan')."\t".' Total user:'.jry_wb_php_cli_color(count($users),'magenta').' Total clients:'.jry_wb_php_cli_color(count($clients),'magenta').' Total c_to_u:'.jry_wb_php_cli_color(count($c_to_u),'magenta')."\n";;
						jry_wb_socket_send($client,(array('code'=>false,'reason'=>500000)));
						socket_close($client);
						continue;			
					}
					$clients[]=$client;
					$c_to_u[]=$user['id'];
					$count=0;
					if($result===false)
					{
						$count=$user['count']=1;
						$users[]=$user;
						$users_id=array_column($users,'id');
						echo jry_wb_php_cli_color($user['id'].'-'.$user['name'],'light_blue')."\t".jry_wb_php_cli_color('connected','green').' at '.jry_wb_php_cli_color($ip."\t".jry_wb_get_ip_address_string($ip),'cyan')."\t".'now have '.jry_wb_php_cli_color($user['count'],'magenta').' connect '.jry_wb_php_cli_color('new','yellow').' Total user:'.jry_wb_php_cli_color(count($users),'magenta').' Total clients:'.jry_wb_php_cli_color(count($clients),'magenta').' Total c_to_u:'.jry_wb_php_cli_color(count($c_to_u),'magenta')."\n";;
					}
					else
					{
						$users[$result]['count']++;
						$count=$users[$result]['count'];
						echo jry_wb_php_cli_color($user['id'].'-'.$user['name'],'light_blue')."\t".jry_wb_php_cli_color('connected','green').' at '.jry_wb_php_cli_color($ip."\t".jry_wb_get_ip_address_string($ip),'cyan')."\t".'now have '.jry_wb_php_cli_color($users[$result]['count'],'magenta').' connect(s) Total user:'.jry_wb_php_cli_color(count($users),'magenta').' Total clients:'.jry_wb_php_cli_color(count($clients),'magenta').' Total c_to_u:'.jry_wb_php_cli_color(count($c_to_u),'magenta')."\n";;
					}
					jry_wb_socket_send($client,(array('code'=>true,'type'=>100000,'data'=>array('count'=>$count))));
				}
			}
			else
			{
				$c_index=array_search($socket,$clients);
				$id=$c_to_u[$c_index];
				$u_index=array_search($id,$users_id);
				$user=$users[$u_index];
				socket_getpeername($client,$ip);					
				$bytes=socket_recv($socket,$buf,1024*1024*10,0);
				$data=jry_wb_socket_decode($buf);
				if ($bytes === FALSE)
					echo jry_wb_php_cli_color('Failed!','light_red').jry_wb_php_cli_color($user['id'].'-'.$user['name'],'light_blue').' On '.jry_wb_php_cli_color('socket_recv()','cyan').' At FILE:'.jry_wb_php_cli_color(__FILE__,'yellow').' LINE:'.jry_wb_php_cli_color(__LINE__,'yellow').' Because '.socket_strerror(socket_last_error())."\n";
				else if($bytes<=6||empty($data)||!is_object(json_decode($data)))
				{					
					$users[$u_index]['count']--;
					unset($clients[$c_index]);
					unset($c_to_u[$c_index]);
					socket_close($socket);
					echo jry_wb_php_cli_color($user['id'].'-'.$user['name'],'light_blue')."\t".jry_wb_php_cli_color('disconnect','yellow').' at '.jry_wb_php_cli_color($ip."\t".jry_wb_get_ip_address_string($ip),'cyan')."\t".'now have '.jry_wb_php_cli_color($users[$result]['count'],'magenta').' connect(s) Total user:'.jry_wb_php_cli_color(count($users),'magenta').' Total clients:'.jry_wb_php_cli_color(count($clients),'magenta').' Total c_to_u:'.jry_wb_php_cli_color(count($c_to_u),'magenta')."\n";
				}
				else
				{
					echo jry_wb_php_cli_color($user['id'].'-'.$user['name'],'light_blue')."\t".jry_wb_php_cli_color('get ','green').jry_wb_php_cli_color(strlen($data),'magenta').'/B data '.substr($data,0,100)."\n";
					$data = json_decode($data);
					if($data->code==false)
					{
						
					}
					else
					{
						if($data->type==100000)
						{
							jry_wb_socket_send($socket,(array('code'=>true,'type'=>'100000')));
						}
						else if($data->type==200000)
						{
							jry_wb_socket_send_to_user($user,$data->data->to,200000,$data->data->message);
						}
					}
	 
				}
			}
		}
	 
	}
	function jry_wb_socket_send($client,$message)
	{
		$message=json_encode($message);
		$b1=0x80|(0x1&0x0f);
		$length=strlen($message);
		if($length<=125)
		{
			$header=pack('CC',$b1,$length);
		}
		elseif($length>125&&$length<65536)
		{
			$header=pack('CCn',$b1,126,$length);
		}
		elseif($length>=65536)
		{
			$header=pack('CCNN',$b1,127,$length);
		}
		$message=$header.$message;
		socket_write($client,$message,strlen($message));
		return $length;
	}
	function jry_wb_socket_decode($text)
	{
		$length=ord($text[1])&127;
		if($length==126) 
		{
			$masks=substr($text,4,4);
			$data=substr($text,8);
		}
		else if($length==127) 
		{
			$masks=substr($text,10,4);
			$data=substr($text,14);
		}
		else
		{
			$masks=substr($text,2,4);
			$data=substr($text, 6);
		}
		$text = "";
		for ($i=0;$i<strlen($data);$i++)
			$text .= $data[$i]^$masks[$i%4];
		return $text;
	}
	function jry_wb_socket_send_to_user($from,$to_id,$type,$data)
	{
		global $users_id;
		global $users;
		global $c_to_u;
		global $clients;
		$to_index=array_search($to_id,$users_id);		
		$to=$users[$to_index];
		$cnt=0;
		$length=0;
		foreach ($c_to_u as $i=>$id)
			if($id==$to['id'])
			{
				$length+=jry_wb_socket_send($clients[$i],array('code'=>true,'type'=>$type,'from'=>$from['id'],'data'=>$data));
				$cnt++;
			}
		echo jry_wb_php_cli_color($from['id'].'-'.$from['name'],'light_blue')."\t".jry_wb_php_cli_color('send ','green').' to '.jry_wb_php_cli_color($to['id'].'-'.$to['name'],'light_blue').' cnt:'.jry_wb_php_cli_color($cnt,'magenta').' total:'.jry_wb_php_cli_color($length,'magenta').'/B'."\n";
	}