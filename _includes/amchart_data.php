<?php
include("common.php");
$dtype = $_GET['dtype'] ? $_GET['dtype'] : "hour";
$ddate = $_GET['ddate'] ? $_GET['ddate'] : date("Ymd");
$ret = array();
if($dtype == 'hour'){
	for($h=0; $h<24; $h++){
		$data = array();
		$hh =str_pad($h,2,"0",STR_PAD_LEFT);
		$data['hour']=$hh;
		$ts = $date.$hh."0000";
		$t = strtotime($ts);
		$rd = show_rd("ifurniture", " where `ddate` = '".$ddate."' and `atime` > ".$t." order by `sn` asc ");
		$data['time'] = $rd['adate'] ? $rd['adate'] : '';
		$data['temper'] = $rd['data1']*1;
		$data['light'] = $rd['data2']*1;
		$data['power'] = $rd['data3']*1;
		$ret[] = $data;
	}
}
echo json_encode($ret);
exit;
?>