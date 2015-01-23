<?php
include("common.php");
$eptypes = array(
	"15"=>array("name"=>"計量插座", "on"=>"fa-plug", "off"=>"fa-plug" ),
	"17"=>array("name"=>"溫溼度計", "on"=>"fa-asterisk", "off"=>"fa-asterisk" ),
	"22"=>array("name"=>"紅外控制", "on"=>"fa-gamepad", "off"=>"fa-gamepad" ),
	"02"=>array("name"=>"紅外感應", "on"=>"fa-bell", "off"=>"fa-bell-slash" )
	);
if($_GET['cmd'] == 'get'){
	$rs = show_rs("devices", " where 1 ");
	$data = array();
	foreach($rs as $rd){
		if(!$rd['location']){
			$rd['location'] = show_column("dev_location", "location", " where `devID` = '".$rd['devID']."' ");
		}
		$ary = json_decode($rd['devInfo'], true);
		$info = $ary[0];
		$info['devID'] = $rd['devID'];
		if(isset($info['epStatus'])){
			$info['status'] = $info['epStatus'] == '1' ? "on" : "off";
		}else{
			$info['status'] = $rd['devStatus'] == '1' ? "on" : "off";
		}
		$info['text'] = $rd['devDataText'];
		$info['info'] = $eptypes[$info['epType']];
		$info['name'] = $info['info']['name'];
		$info['icon'] = $info['info'][$info['status']];
		$info['html'] = $rd['devDataText'] ? nl2br($rd['devDataText']) : $info['name'];
		$info['location'] = $rd['location'];
		$info['openCtrlData'] = $rd['openCtrlData'];
		$info['closeCtrlData'] = $rd['closeCtrlData'];
		if($rd['location']){
			list($info['x'], $info['y']) = explode(",",$rd['location']);
		}
		$data[] = $info;
	}
	$rs = show_rs("crestron", " where 1 ");
	foreach($rs as $rd){
		if(!$rd['location']){
			$rd['location'] = show_column("dev_location", "location", " where `devID` = 'crestron".$rd['id']."' ");
		}
		$info = array();
		$info['devID'] = "crestron".$rd['id'];
		$info['epType'] = "crestron";
		$info['status'] = "on";
		$info['text'] = "Crestron";
		$info['name'] = "Crestron";
		$info['icon'] = "crestron";
		$info['html'] = "crestron".$rd['id'];
		$info['ip'] = $rd['ip'];
		$info['location'] = $rd['location'];
		if($rd['location']){
			list($info['x'], $info['y']) = explode(",",$rd['location']);
		}
		$data[] = $info;
	}
}elseif($_GET['cmd'] == 'chg_location'){
	$devID = $_POST['devID'];
	$location = $_POST['location'];
	$lrd = show_rd("dev_location", " where `devID` = '".$devID."' ");
	if(!$lrd){
		Insert_Set("dev_location", array("devID"=>$devID, "location"=>$location));
	} 
	if(substr($devID, 0, 8) == 'crestron'){
		$id = str_replace("crestron", "", $devID);
		Update_Table("crestron",array("location"=>$location), " where `id` = '".$id."' ");
	}else{
		Update_Table("devices",array("location"=>$location), " where `devID` = '".$devID."' ");
	}
	$data = array("status"=>"ok");
}elseif($_GET['cmd'] == 'control'){
	$devID = $_POST['devID'];
	$ctrlcode = $_POST['ctrlcode'];
	$url = 'http://localhost:8080/Wulian2Sky/Transervlet?cmd=control&strGwID=C4E666456F08&strDevType&strDevID='.$devID.'&strCtrlData='.$ctrlcode;
	$rs = get_curl($url);
	$data = array("status"=>"ok");
}elseif($_GET['cmd'] == 'add_crestron'){
	$crestron_id = Insert_Set('crestron', array("ip"=>$_POST['ip'], "name"=>$_POST['name']), "id");
	$data = array("status"=>"ok", "devID"=>"crestron".$crestron_id);
}
echo json_encode($data);
exit;
?>