<?php
	include_once("jry_wb_cli_includes.php");
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