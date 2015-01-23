<?php
ini_set('display_errors',true);
date_default_timezone_set("Asia/Taipei");
session_start();
ob_end_flush();
$incpath = dirname(__FILE__);
$SitesUrl = "Manage system";
$SitesName = "Daily Progress Records";
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0;
$where = " where 1 ";
$total = 0;
$perpage = isset($_GET['perpage']) ? $_GET['perpage'] : 20;
$start = $page*$perpage;
$self = $_SERVER['QUERY_STRING']? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME']."?f=1";
$thistime = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$nowtime = time();
$fromIP=$HTTP_SERVER_VARS["REMOTE_ADDR"];
$option = array();
include($incpath."/db_inc.php");
include($incpath."/functions.php");
?>