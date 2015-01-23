<?php
function db_conn(){
	$DB_HOST = 'localhost';
	$DB_USER = 'root'; //root
	$DB_PASSWORD = 'wulian'; //-pl,mko0
	$DB_DATABASE = 'wulian';
	$link = mysql_pconnect($DB_HOST, $DB_USER, $DB_PASSWORD) 
	 or die('無法連接到資料庫：'.mysql_error());
	mysql_select_db($DB_DATABASE)
	 or die('無法選擇資料庫['.$DB_DATABASE.']：'.mysql_error());
	sql_query("SET NAMES UTF8");
	return $link;
}
function db_close($link){
	mysql_close($link);
	return true;
}
function sql_query($sql){
	/*
	$ses_id = session_id(); 
	global $nowtime;
	$msc=microtime(true);
	$rs = mysql_query($sql);
	$msc=microtime(true)-$msc;
	$cmd = " insert into `QLOGS` (`sqlcmd`, `sessid`,`adate`, `msecs`) values('".$sql."', '".$ses_id."', '".$nowtime."', '".$msc."')";
	mysql_query($cmd);
	*/
	$rs = mysql_query($sql);
	return $rs;
}
function sql_fetch_assoc($rs){
	$rd = mysql_fetch_assoc($rs);
	return $rd;
}
function sql_num_rows($rs){
	$n = mysql_num_rows($rs);
	return $n;
}
/* 計算特定欄位值加總 */
function Show_Sum($table,$column,$where,$debug = 0)
{
	$sql = " select sum( ".$column." ) as SUMNUM from ".$table." ".$where;
	if($debug)echo $sql."<br>";
	$row = sql_query_rd($sql);
	$sum = $row['SUMNUM'];
	return $sum > 0 ? $sum  : 0;
}
/* 計算數量 */
function Show_Num($table,$where,$debug = 0)
{
	$sql = " select count(*) as CNT from ".$table." ".$where;
	if($debug)echo $sql."<br>";
	$row = sql_query_rd($sql);
	$countnum = $row['CNT'];
	return $countnum >0 ? $countnum : 0;
}
/* 顯示特定欄位值 */
function Show_Column($table,$column,$where,$debug = 0)
{
	$sql = " select ".$column." from ".$table." ".$where." limit 1 ";
	if($debug)echo $sql."<br>";
	$row = sql_query_rd($sql);
	$colvalue = $row[$column];
	return $colvalue;
}
/* 新增表資料 */
function Insert_Sn($table, $column='id', $debug=false){ //平常不用回傳新增的sn, 若要回傳, $column請帶一個可以輸入 varchar(32)以上的
	$sql = " select max(".$column.") as NEWSN from `".$table."` ";
	if($debug)echo $sql."<br>";
	$rd = sql_query_rd($sql);
	$newsn = $rd['NEWSN']+1;
	$sql = " insert into `".$table."` (`".$column."`) values( '".$newsn."') ";
	sql_query($sql);
	return $newsn;
}

function Insert_Set($table, $data, $pkey='',$debug=''){
	if(!is_array($data)) return false;
	$fields = get_field_name($table);
	foreach($data as $k => $v){
		if(in_array($k, $fields)){
			if(++$r > 1) $updstr .= ",";
			$updstr .= " `".$k."` = '".my_escape_string($v)."' ";
		}
	}
	$updsql = " insert into `".$table."` set ".$updstr." ";
	if($debug) echo "updsql: ".$updsql."<br>";
	sql_query($updsql);
	if($pkey){
		$last_id = mysql_insert_id();
		$sql = " select max(".$pkey.") as NEWSN from `".$table."` ";
		$rd = sql_query_rd($sql);
		return $rd['NEWSN'];
	}
	return true;
}

/* 更新表單資料 */
function Update_Table($tbname,$data = array(),$where, $debug = 0){
	if(!is_array($data)) return false;
	$col_array = get_field_name($tbname);
	$rd = Show_rd($tbname, $where);
	if(is_array($rd)){
		foreach($col_array as $k){
			$v = $rd[$k];
			if(isset($data[$k]) && ($data[$k] != $v)){
				$data[$k] = my_escape_string($data[$k]);
				if(++$r > 1) $updstr .= ",";
				if($data[$k] != 'NULL'){
					$updstr .= " ".$k." = '".$data[$k]."' ";
				}else{
					$updstr .= " ".$k." = NULL ";
				}
			}
		}
		if($updstr){
			$updsql = " update ".$tbname." set ".$updstr." ".$where;
			if($debug)echo $updsql ."<br />";
			$stmt = sql_query($updsql);
		}
		return true;
	}else{
		return false;
	}
}

function get_field_name($table){
	$sql = "SELECT * FROM `".$table."` limit 1 "; 
	$rs = sql_query($sql); 
	$columns = mysql_num_fields($rs); 
	for($i = 0; $i < $columns; $i++) { 
		$col_arrays[] = mysql_field_name($rs, $i); 
	}
	return $col_arrays;
}

//---------------2008-10-29 by Rick

function del_row($tbname,$where){
	Delete_Table($tbname,$where);
}
/* 刪除資料表資料 */
function Delete_Table($tbname,$where){
	$sql = " delete from ".$tbname." ".$where;
	/*echo $sql;*/
	$stmt = sql_query($sql);
	return true;
}
/* 取得多筆資料陣列 */
function show_rs($table, $where, $order="", $usepage=0, $perpage=20, $col_array=NULL, $debug=0){
	if(is_array($col_array)) $cols_str = implode(",",$col_array);
	if(!$cols_str) $cols_str = '*';
	$sql = " select ".$cols_str." from ".$table." ".$where." ".$order;
	if($usepage){
		global $page;
		$cur_start = $page*$perpage;
		$pagesql = " limit ".$cur_start.", ".$perpage." ";
		$sql .= $pagesql;
	}
	$rs = sql_query($sql);
	if($debug) echo "sql=".$sql;
	//if(!$rs) echo "sql=".$sql;
	while($rd = mysql_fetch_assoc($rs)){
		$array[] = $rd;
	}
	if (!is_array($array)) $array = array();
	return $array;
}
/* 取得一筆資料陣列(表格、條件敘述) */
function show_rd($table, $where, $debug=0)
{
	$sql = " select * from ".$table." ".$where." limit 1 ";
	if($debug) echo $sql;
	$rs = sql_query($sql);
	$rd = mysql_fetch_assoc($rs);
	return $rd;
}
function sql_query_rd($sql, $debug=0){
	$rs = sql_query($sql);
	if($debug) echo $sql."<br />";
	$row = mysql_fetch_assoc( $rs );
	//if(!$row) echo $sql;
	return $row;
}

//手動輸入的sql語法，但一次傳回陣列的參數內容(所以若有分頁換頁，請自行在sql語法中搞定!)
function sql_query_rs($sql, $debug=0){
	if($debug) echo $sql;
	$rs = sql_query($sql);
	while($rd = mysql_fetch_assoc($rs)){
		$array[] = $rd;
	}
	if (!is_array($array)) $array = array();
	return $array;
}

function sql_query_num($sql){
	$sql = " select count(*) as CNT from ( ".$sql." ) TMPTB ";
	$rd = sql_query_rd($sql);
	return $rd['CNT'];
}
function get_array($table, $key_col='id', $val_col = '', $where='', $orderby=''){
	$ret = array();
	$sel_col = "*";
	if($val_col) $sel_col = $key_col.", ".$val_col." ";
	if(!$orderby) $orderby = " order by `".$key_col."` asc ";
	$sql = " select ".$sel_col." from `".$table."` ".$where.$orderby;
	//echo $sql;
	$rs = sql_query($sql);
	while($rd = mysql_fetch_assoc($rs)){
		if($val_col){
			$ret[$rd[$key_col]] = $rd[$val_col];
		}else{
			$ret[$rd[$key_col]] = $rd;
		}
	}
	return $ret;
}
function my_escape_string($data) {
	if ( !isset($data) or strlen($data) <=0 ) return '';
	if ( is_numeric($data) ) return $data;
	$non_displayables = array(
		'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/',             // url encoded 16-31
		'/[\x00-\x08]/',            // 00-08
		'/\x0b/',                   // 11
		'/\x0c/',                   // 12
		'/[\x0e-\x1f]/'             // 14-31
	);
	foreach ( $non_displayables as $regex )
		$data = preg_replace( $regex, '', $data );
	$data = str_replace("'", "''", $data );
	return $data;
}
$link = db_conn();
?>
