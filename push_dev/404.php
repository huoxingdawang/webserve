<?php $dir=dirname(__FILE__).'/jry_wb/';include_once($dir.'jry_wb_configs/jry_wb_config_includes.php');include_once($dir.'tools/jry_wb_save_browsing_history.php');if($_SERVER['HTTP_HOST']!=JRY_WB_DOMIN.(JRY_WB_PORT==''?'':':').JRY_WB_PORT){header("Location:".JRY_WB_HOST);exit();}?><style type="text/css">.spinner{width:200px;height:100px;text-align:center;font-size:10px;}.spinner > div{margin:2px;background-color:#3498db;height:100%;width:15px;display:inline-block;-webkit-animation:stretchdelay 1.2s infinite ease-in-out;animation:stretchdelay 1.2s infinite ease-in-out;}.spinner .rect2{-webkit-animation-delay:-1.1s;animation-delay:-1.1s;}.spinner .rect3{-webkit-animation-delay:-1.0s;animation-delay:-1.0s;}.spinner .rect4{-webkit-animation-delay:-0.9s;animation-delay:-0.9s;}.spinner .rect5{-webkit-animation-delay:-0.8s;animation-delay:-0.8s;}@-webkit-keyframes stretchdelay{0%,40%,100%{-webkit-transform:scaleY(0.4)}20%{-webkit-transform:scaleY(1.0)}}@keyframes stretchdelay{0%,40%,100%{transform:scaleY(0.4);-webkit-transform:scaleY(0.4);}20%{transform:scaleY(1.0);-webkit-transform: scaleY(1.0);}}</style><body bgcolor="#000" style="background-color:#000;"><div style="font-size:200px; color:#F00;background-color:#000;" align="center">WARNING!<br></div><div style="font-size:50px; color:#F00;background-color:#000;" align="center">You are trying to get something that we don't have.<br>Please stop now!<br>:-(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)-:</div><div style="font-size:25px; color:#F00;background-color:#000;" align="center">If you sure that the URL:<?php if($_GET['url']!='')echo $_GET['url'];else echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?> is right,send an email to develop group.<br><a href="http://<?php echo $_SERVER['HTTP_HOST']?>/jry_wb/jry_wb_mainpages/index.php">Click here to back</a><br><span style="color:white;"><?php  echo $error.$_GET['error'];?></span></div></body>