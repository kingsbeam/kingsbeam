<?php
include("common.php");
if(!is_numeric($_GET['last_sn'])){
	$rs = show_rs("devices", " where 1 ");
}else{
	$show_rs = show_rs("devices", " where `sn` > '".$_GET['last_sn']."' ");
}
echo json_encode($rs);
exit;
?>