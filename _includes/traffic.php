<?php
/**/
$fid = $_GET['fid'] ? $_GET['fid'] : '10010';
include("simple_html_dom.php");
$html = new simple_html_dom();

// Load HTML from a string
$html->load_file('http://1968.freeway.gov.tw/traffic/index/fid/'.$fid);
$secs = $html->find('#secs_body',0)->children;
$res = array();
foreach($secs as $tr){
	$sec_name= $tr->find('.sec_name',0)->plaintext;
	$speed_left= $tr->find('td',0)->plaintext;
	$speed_right= $tr->find('td',2)->plaintext;
	$res[] = array('sec_name'=>$sec_name, 'speed_left'=>$speed_left, 'speed_right'=>$speed_right );
}
echo json_encode($res);
?>