<?php
include("common.php");
$dtype = $_POST['dtype'] ? $_POST['dtype'] : "5min";
$ddate = $_POST['ddate'] ? date("Ymd",strtotime($_POST['ddate'])) : date("Ymd");
$dhour = $_POST['dhour'] ? $_POST['dhour'] : date("G");
$data['labels'] = array();
$data['temper'] = array();
$data['power'] = array();
$data['ddate'] = date("Y-m-d",strtotime($ddate));
if($dtype == 'hour'){
	
}
echo json_encode($data);
exit;
?>