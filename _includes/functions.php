<?php
function array2br($array, $sep = '<br />'){
	$ret = '';
	$i =0;
	foreach($array as $v){
		if(++$i > 1) $ret .=  $sep."\n";
		$ret .=  $v;	
	}
	$ret .= "\n";
	return $ret;
}

function ShowSize($filesize)
{
	if($filesize > 1000000)
	{
		$xsize = ($filesize/1024)/1024;
		$showsize = sprintf("%01.2f",$xsize)."&nbsp;MB";
	}
	elseif($filesize > 1000)
	{
		$xsize = $filesize/1024;
		$showsize = sprintf("%01.2f",$xsize)."&nbsp;KB";
	}
	else
	{
		$xsize = $filesize;
		$showsize = sprintf("%01.2f",$xsize)."&nbsp;byte";
	}
	return $showsize;
}

function php_mailer_send($to_array, $subject, $body, $from="", $BCC = "" ){
	require_once($_SERVER['DOCUMENT_ROOT']."/../_class/class.phpmailer.php");
	if(!$from['from_email']) $from['from_email'] = SERVICE_EMAIL;
	if(!$from['from_name']){
		global $SitesName;
		$from['from_name'] = $SitesName;
	}
	$mail = new PHPMailer();
	$mail->Subject = $subject;
	$mail->AltBody = strip_tags($body);
	$mail->MsgHTML($body);
	//$mail->SMTPDebug = true;
	if($BCC) $mail->AddBcc($BCC); 
	if(is_array($to_array)){
		foreach($to_array as $to_mail){
			$mail->AddAddress($to_mail);
		}
	}else{
		$mail->AddAddress($to_array);
	}
	$sendrs = @$mail->Send();
	$mail->ClearAddresses();
	return $sendrs;
}

function alert($msg){
	echo "<script>alert('".$msg."')</script>";
}

function Del_Backpage($delparam){
	$msg= "Del_Backpage=".$delparam;
	alert($msg);
	$qstr = str_replace("&".$delparam."=".$_GET[$delparam],"",$_SERVER['QUERY_STRING']);
	echo "<script>location.href='".$_SERVER['PHP_SELF']."?".$qstr."'</script>";
	exit;
}
function Del_Loadpage($delparam){
	$qstr = str_replace("&".$delparam."=".$_GET[$delparam],"",$_SERVER['QUERY_STRING']);
	echo "<script>load_page('".$_SERVER['PHP_SELF']."?".$qstr."');</script>";
	exit;
}
function location($url){
	echo "<script>location.href='".$url."'</script>";
}
function load_page($url){
	echo "<script>load_page('".$url."');</script>";
}
function MakePass($length, $type = '')
{
	if($type == 'upper'){
		$possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	}elseif($type == 'lower'){
		$possible = "abcdefghijklmnopqrstuvwxyz";
	}elseif($type == 'num'){
		$possible = "0123456789";
	}else{
		$possible = "0123456789". 
		"abcdefghijklmnopqrstuvwxyz". 
		"ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
	}
	$str = ""; 
	while(strlen($str) < $length) 
	{ 
		$str .= substr($possible, (rand() % strlen($possible)), 1); 
	} 
	return($str); 
}
function get_download_file( $file_name, $file_size, $file_path='', $content='' ){
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);
	header('Content-Type: application/octet-stream');
	header('Content-Length: ' . $file_size);
	header('Content-Disposition: attachment; filename="' . $file_name . '";');
	header('Content-Transfer-Encoding: binary');
	if($file_path){
		readfile($file_path);
	}elseif($content){
		echo $content;	
	}
}
function header_string(){
	$header_string = 'Content-Type: text/html; charset=utf-8';
	header($header_string );
}
function trim_array($array){
	$narray = array();
	if(is_array($array)){
		foreach($array as $k => $v){
			$narray[$k] = trim($v);
		}
	}else{
		$narray = $array;
	}
	return $narray;
}
function post_curl($url, $params){
	if(is_array($params)){
		$z = 0;
		foreach($params as $k=>$v){
			if(++$z > 1) $string .= "&";
			$string .= $k."=".$v;
		}
	}else{
		$string = $params;
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $string);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec ($ch);
	if ($data) {
        curl_close ($ch);
		return $data;
   } else {
       $ret = "error!".curl_error($ch);
	   curl_close ($ch); 
	   return $ret;
   }
}
function get_curl($url, $port=8080){

		$user_agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2)";
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);              // Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_PORT, $port);            //Set the port number
		curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s
		
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		$doc = curl_exec($ch);
		curl_close ($ch); 
	//return $doc;
}

//居住地 
function Show_CityArea($sca,$where=''){
	$ca_array = array();
	$sql = "select distinct ".$sca." from TB_CITYAREA ".$where;
	$stmt = sql_querys($sql);
	while($rd= sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC )){
		$ca_array[$rd[$sca]] = $rd[$sca];
	}
	return $ca_array;
}


//對照地址的座標
function get_location($addr){
	$pos= array();
	/* 以下是使用google 的 api: */
	$url = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($addr)."&sensor=true";
	$rs = get_curl($url);
	$js_ary = json_decode($rs);
	$geo = $js_ary->results[0]->geometry->location;
	$pos['lat'] = $geo->lat;
	$pos['lng'] = $geo->lng;

	return false;
}

function get_weather($lat,$lng){
	$pos= array();
	$url = "http://www.google.com/ig/api?hl=zh-tw&weather=,,,".$lat.",".$lng;
	$rs = get_curl($url);
	$start_pos = strpos($rs, "<current_conditions>");
	$end_pos = strpos($rs, "</current_conditions>");
	$cont = substr($rs, $start_pos+20, $end_pos-$start_pos-20);
	$cont=str_replace("/>","",$cont);
	$ary = split("<",$cont);
	$winfo = array();
	if(is_array($ary)){
		foreach($ary as $eqstr){
			if(strlen($eqstr) > 1){
				$vstr = split("=",$eqstr);
				$winfo[$vstr[0]] = str_replace("\"","",$vstr[1]);
			}
		}
	}
	print_r($winfo);
}

function get_address($lat,$lng,$item=''){
	global $docroot_relpath;
	/*使用yahoo api:*/
	$yahoo_appid = '1xbko1_V34FmJ_Bh4tsWZyQbyCbPbDa3CaBBe4tFDkmEAtCsFI.9RouQL9Du2ly9';
	$url = "http://where.yahooapis.com/geocode?location=".$lat."+".$lng."&flags=P&gflags=R&appid=".$yahoo_appid;
	
	//$url = "http://maps.google.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=false&region=tw";
	$rs = get_curl($url);
	$ary = unserialize($rs);
	print_r($ary);
	if(!$item) return $ary;
	if($item == 'zipcode'){
		$zipstr = $ary['ResultSet']['Result'][0]['uzip'];
		$zipcode = substr($zipstr,0,3);
		if(is_numeric($zipcode)) return $zipcode;
	}
}

function get_subname($f, $type='images'){
	if($type == 'images'){
		$size = getimagesize($f);
		global $picsub_array;
		return $picsub_array[$size[2]];
	}else{
		$subs = explode(".",$f['name']);
		if(is_array($subs)) return array_pop($subs);
	}
}
function get_image_info($f){
	$fs = getimagesize($f);
	$size['width'] = $fs[0];
	$size['height'] = $fs[1];
	if($fs['mime'] == 'image/gif'){ //gif
		$size['subname'] = 'gif';
	}elseif($fs['mime'] == 'image/jpeg'){
		$size['subname'] = 'jpg';
	}elseif($fs['mime'] == 'image/png'){
		$size['subname'] = 'png';
	}else{
		return false;
	}
	return $size;
}
function check_integer($str){
	$ret = array("rs"=>true,"err"=>'');
	if(!is_numeric($str)){
		$ret = array("rs"=>false,"err"=>"必需是數字");	
	}elseif($str <= 0){
		$ret = array("rs"=>false,"err"=>"必需大於零");	
	}elseif(ceil($str) > $str){
		$ret = array("rs"=>false,"err"=>"必需為整數");	
	}
	return $ret;
}

function show_date($timestamp,$showtime=0){
	$str = '';
	if(is_numeric($timestamp) && $timestamp != 0){
		$str = date("Y-m-d",$timestamp);
		if($showtime) $str = date("Y-m-d H:i",$timestamp);
	}
	return $str;
}

function sec_to_datstr($sec_str){
	$days = floor($sec_str/86400);
	$hours_secs = $sec_str%86400;
	$hours = floor($hours_secs/3600);
	$mins_secs = $hours_secs%3600;
	$mins = floor($mins_secs/60);
	$secs = $mins_secs%60;
	$day_str = '';
	if($days > 0 ) $day_str = $days.'天';
	elseif($hours > 0 ) $day_str = $hours.'小時';
	elseif($mins > 0 ) $day_str = $mins.'分';
	elseif($secs > 0 ) $day_str = $secs.'秒';
	return $day_str;
}
function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*' ); //,'%27'=>"'" , '%28'=>'(', '%29'=>')'
    return strtr(rawurlencode($str), $revert);
}
//------------------2008-10-29 by Rick
function CHK_PID($pid){ 	//身分証字號檢核
	$tab = " ABCDEFGHJKLMNPQRSTUVXYWZIO";
	$A01 = array(1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3);
	$A02 = array(0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5);
	$Mx = array(9,8,7,6,5,4,3,2,1,1);
	
	if (strlen($pid) != 10) return false;
	$pid = strtoupper($pid);
	if (!($i = strpos($tab, substr($pid, 0, 1)))) return false;
	$sum = $A01[$i - 1] + $A02[$i - 1] * 9;
	
	for ($i = 1; $i < 10; $i++){
		$v = substr($pid, $i, 1);
		if (($v < '0') || ($v > '9')) return false;
		$sum = $sum + $v * $Mx[$i];
	}
	if ($sum % 10 != 0) return false;
	return true;
}
function CHK_EMAIL($email){
	return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email);
}

//$crop_coords = array(x,y,x2,y2);
function image_crop($f, $savepath, $crop_coords, $new_w, $new_h, $fix_pic_w = 400){
	$jpeg_quality = 100;
	$fsize = getimagesize($f);
	if($fsize['mime'] == 'image/gif'){ //gif
		$img_r = imagecreatefromgif($f); 
	}elseif($fsize['mime'] == 'image/jpeg'){
		$img_r = imagecreatefromjpeg($f);
	}elseif($fsize['mime'] == 'image/png'){
		$img_r = imagecreatefrompng($f);
	}else{
		return false;
	}
	
	$dst_r = imagecreatetruecolor( $new_w, $new_h );
	//計算取樣的起點和寬高
	$r =1;
	if($fsize[0] > $fix_pic_w or $fsize[1] > $fix_pic_w){
		$r = $fsize[0]>= $fsize[1] ? $fsize[0]/$fix_pic_w : $fsize[1]/$fix_pic_w;
	}
	$x = floor($r * $crop_coords['x']);
	$y = floor($r * $dcrop_coordsata['y']);
	$w = floor($r * ($crop_coords['x2']-$crop_coords['x']));
	$h = floor($r * ($crop_coords['y2']-$crop_coords['y']));
	imagecopyresampled($dst_r,$img_r,0,0,$x,$y, $new_w, $new_h, $w, $h);
	header('Content-type: image/jpeg');
	imagejpeg($dst_r,$savepath, $jpeg_quality);
	return;
}

//縮圖程式
function resize($filename, $dest, $width, $height){
	$fsize = getimagesize($filename);
	$org_width=$fsize[0];
	$org_height=$fsize[1];
	$subname = $fsize['mime'];
	switch($subname){
		case 'image/gif' :
			$type ="gif";
			$img = imagecreatefromgif($filename);
		break;
		case 'image/png' :
			$type ="png";
			$img = imagecreatefrompng($filename);
		break;
		case 'image/jpeg':
			$type ="jpg";
			$img = imagecreatefromjpeg($filename);
		break;
		default :
			die ("ERROR; UNSUPPORTED IMAGE TYPE".$subname);
		break;
	}
	
	$xoffset = 0;
	$yoffset = 0;
	if ($org_width / $width > $org_height/ $height){
		 $xtmp = $org_width;
		 $xratio = 1-((($org_width/$org_height)-($width/$height))/2);
		 $org_width = $org_width * $xratio;
		 $xoffset = ($xtmp - $org_width)/2;
	}elseif ($org_height/ $height > $org_width / $width){
		 $ytmp = $org_height;
		 $yratio = 1-((($width/$height)-($org_width/$org_height))/2);
		 $org_height = $org_height * $yratio;
		 $yoffset = ($ytmp - $org_height)/2;
	}
	$img_n=imagecreatetruecolor ($width, $height);
	if( function_exists( 'imageantialias' )){ @imageantialias( $img_n, true ); }
	imagecopyresampled($img_n, $img, 0, 0, $xoffset, $yoffset, $width, $height, $org_width, $org_height);
	if($type=="gif"){
		imagegif($img_n, $dest);
	}elseif($type=="jpg"){
		imagejpeg($img_n, $dest);
	}elseif($type=="png"){
		imagepng($img_n, $dest);
	}elseif($type=="bmp"){
		imagewbmp($img_n, $dest);
	}
	return true;
}

function s_array(&$array) {
	if (is_array($array)) {
		foreach ($array as $k => $v) {
			$array[$k] = s_array($v);
		}
	}else if (is_string($array)) {
		$array = stripslashes($array);
	}
	return $array;
}
function set_cmd($table){
	if($_POST['cmd']){
		if(is_array($_POST['lst'])){
			foreach($_POST['lst'] as $k =>$v){
				switch($_POST['cmd']){
					case 'del':
						del_row($table," where `sn` ='".$v."'");
					break;
					case 'display_on':
						update_table($table,array('display'=>'Y')," where `sn` ='".$v."' ");
					break;
					case 'display_off':
						update_table($table,array('display'=>'N')," where `sn` ='".$v."' ");
					break;
				}
			}
		}else{
			alert("請勾選要變動的項目");
		}
	}
}
function proc_online_status($table){
	global $nowtime;
	$sql = " update `".$table."` set `online_status` = 'Y' where ( `online` is null or `online` <= '".$nowtime."' ) and (`offline` is null or `offline` = 0 or `offline` > '".$nowtime."' ) ";
	sql_query($sql);
	$sql = " update `".$table."` set `online_status` = 'N' where ( `online` > '".$nowtime."' ) or (`offline` is not null and `offline` > 0 and `offline` < '".$nowtime."' ) ";
	sql_query($sql);
	return true;
}
function check_online($p_online, $p_offline){
	global $nowtime;
	if( (!is_numeric($p_online) || $p_online <= $nowtime) &&  ( !is_numeric($p_offline) or $p_offline > $nowtime) ){
		return true;
	}
	return false;
}
function check_validdate($date)
{
    $date = str_replace("/","-",$date);
	if(strlen($date) == 8) $date= substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
	if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
    { 
        if (checkdate($matches[2], $matches[3], $matches[1]))
        { 
            return true; 
        } 
    } 
    return false;
}
function check_validmail($mail){
	if(preg_match("/^([-.0-9a-z_]+)@([-0-9a-z]+).([.0-9a-z]+)$/i", $mail)) return true;
	return false;
}
function check_errors($required_format, $data){
	$errors = array();
	foreach($required_format as $k => $v){
		if(!$data[$k]) $errors[$k] = "不可空白";
		if(!$errors[$k]){
			//拆解v
			$types = explode(",",$v);
			foreach($types as $type){
				if($errors[$k]) continue;
				if(preg_match("/~/",$type)){ //含有~為長度驗證
					$length_range = explode("~", $type); //拆開上下限
					$data_length = strlen($data[$k]);
					if($data_length > $length_range[1] or $data_length < $length_range[0]){
						$errors[$k] = "長度錯誤".$type;
					}
				}elseif($type == 'email'){
					if(!check_validmail($data[$k]))$errors[$k] = "電子郵件格式錯誤";
				}elseif($type == 'date'){
					if(!check_validdate($data[$k])) $errors[$k] = "日期錯誤";
				}elseif($type == 'session'){
					if($data[$k] != $_SESSION[$k]) $errors[$k] = "不符合比對";
				}
			}
		}
	}
	if($errors) return $errors;
	return false;
}
function get_bonus_config(){
	$rs = show_rs("SYS_CONFIG", " where `name` = 'bonus' ");
	foreach($rs as $rd){
		$bonus[$rd['title']] = $rd['content'];
	}
	return $bonus;
}

function get_oplace($addr){
	$oplace = 'local';
	$storecity = mb_substr($addr, 0 ,3,"UTF-8");
	if($storecity == '澎湖縣' or $storecity == '金門縣' or $storecity == '連江縣' ){
		$oplace = "foreign";
	}elseif( $storecity =="台東縣" ){
		 $storearea =  mb_substr($addr, 3 ,3,"UTF-8");
		 if($storearea == '綠島鄉' or $storearea == '蘭嶼鄉' ){
			 $oplace = "foreign";
		 }
	}
	return $oplace;
}

function get_sale_price($rd, $sprice=0, $spec=''){
	global $tb_sale_psn;
	global $tb_buysale;
	$new_price = $sprice;
	if(!$new_price){
		if(!$spec){
			$new_price = $rd['sprice'];
		}else{
			$new_price = show_column("PRODUCT_SPEC", "sprice", " where `psn` = '".$rd['sn']."' and `name_ch`= '".$spec."' ");
		}
	}
	if(!$new_price) $new_price = show_column("PRODUCT_SPEC", "sprice", " where `psn` = '".$rd['sn']."' order by `sprice` desc ");
	$sp_rs = show_rs($tb_sale_psn, " where `psn` = '".$rd['sn']."' and `sale_type` = 'buysale' ");
	foreach($sp_rs as $sp_rd){
		$sale_rd = show_rd($tb_buysale, " where `sn` = '".$sp_rd['sale_sn']."' ");
		if( $sale_rd['mstatus'] == 'Y' && $sale_rd['online_status'] == 'Y' ){
			$buysale_rd = $sale_rd;
		}
	}
	if($buysale_rd){
		$discount = $buysale_rd['discount1'];
		$new_price = $discount > 1 ? $new_price - $discount : round($new_price*$discount);
	}
	return $new_price;
}

function set_product_stock_label($psn, $setback=''){
	global $tb_product;
	global $tb_product_spec;
	$soldout = "Sold Out";
	$recharge = "再入荷";
	$instock = "在庫";
	$new_stat = false;
	$prd = show_rd($tb_product, " where `sn` = '".$psn."' ");
	$pstatus = explode(",",$prd['pstatus']);
	$stock = show_sum($tb_product_spec, "stocks", " where `psn` = '".$psn."' and `stocks` >= 0 ");
	$new_pstatus = array();
	$origin = false;
	foreach($pstatus as $ps){
		if($ps != $instock && $ps != $recharge && $ps != $soldout ){
			$new_pstatus[] = $ps;
		}else{
			$origin = $ps;
		}
	}
	if($stock <= 0){
		//將「在庫」或「再入荷」去掉,換成Sold Out
		$new_stat = $soldout;
		$new_pstatus[]  =$new_stat;
	}elseif(in_array($soldout, $pstatus)){ //先前有Sold out 現在改成再入荷
		if($setback){
			$new_stat = $origin;
			$new_pstatus[] = $origin;
		}else{
			$new_stat = $recharge;
			$new_pstatus[]  =$new_stat;
		}
	}
	if($new_stat){
		$upd['pstatus'] = implode(",", $new_pstatus);
		$upd['stocks'] = $stock > 0 ? $stock:0;
		Update_Table($tb_product, $upd, " where `sn` = '".$psn."' ");
	}
	return $new_stat;
}
function get_files($path){
	$files = array();
	foreach( new DirectoryIterator($path) as $file) {
		$filename = $file->getBasename();
		if( $file->isFile() && substr($filename,0,1) !== '.') {
			$subname = pathinfo($filename, PATHINFO_EXTENSION);
			$f = array('name'=>$file->getBasename(),'fsize'=>$file->getSize(),'subname'=>strtolower($subname) );
			$files[] = $f;
		}
	}
	return $files;
}
function send_sms($mobile, $msg, $rel){ //$relsn 用來對應本次發送的資料來源
	$sms_username = "123123123"; //要改!!!!!
	$sms_password = "123123123"; //要改!!!!!
	global $SitesUrl;
	$vldtime = date("YmdHis", strtotime(" + 12 Hours"));
	$responseurl = $SitesUrl."/sms_return.php";
	$url = "http://smexpress.mitake.com.tw/SmSendGet.asp?username=".$sms_username."&password=".$sms_password."&dstaddr=".$mobile."&encoding=UTF8&DestName=".$rel."&dlvtime=&vldtime=".$vldtime."&smbody=".urlencode($msg)."&response=".$responseurl;
	$rs = get_curl($url,9600);
	return $rs;
}
function append_url($href, $addcol, $addval){
	$href_info = parse_url($href);
	$cnct = $href_info['query'] ? "&" : "?";
	$href = $href.$cnct.$addcol."=".$addval;
	return $href;
}
function chk_login(){
	if(!$_SESSION['MSN']) location("/");
}
function get_days_array($sdate, $edate=''){
	$days = 0;
	$days_array[] = $sdate;
	if($edate){
		$stime = strtotime($sdate);
		$etime = strtotime($edate);
		$days = ($etime - $stime) / 86400;
	}
	for($d = 0; $d < $days; $d++){
		$days_array[] = date("Y-m-d", strtotime(" + ".($d+1)." days", $stime));
	}
	return $days_array;
}
function get_weeks_array($sdate, $edate= ''){
	$days = get_days_array($sdate, $edate);
	$weeks_array = array();
	$weeks_names = array();
	foreach($days as $day){
		$cur_year = date("Y", strtotime($day));
		$cur_week =  date("W", strtotime($day));
		if(!in_array($cur_week, $weeks_array)){
			$weeks_array[] = $cur_week;
			$week_start = $day;
			$week_end = date("Y-m-d", strtotime($cur_year."W".$cur_week." + 6 days"));
			$weeks_names[$cur_week] = array("week"=>date("Y")."第".$cur_week."週(".$week_start."~".$week_end.")", "start"=>$week_start, "end"=>$week_end);
		}
	}
	return $weeks_names;
}
function chk($ary){
	if(is_array($ary)){
		foreach($ary as $k => $v){
			$r[$k] = chk($v);
		}
	}else{
		$r = mysql_real_escape_string($ary);
	}
	return $r;
}
function ret($ary, $status='ok'){
	if(is_array($ary)){
		if(!$ary['status']) $ary['status'] = $status;
	}else{
		$ary = array('status'=>$status, 'msg'=>$ary);
	}
	echo json_encode($ary);
	exit;
}
?>