<?php
/*
Template Name: Module Plan Functions
*/
?>
<?php
global $wpdb;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
if(isset($_GET["ur"])) { $username=$_GET["ur"]; }
if(isset($_GET["mc"])) { $modulecode=explode(",", $_GET["mc"]); }
if(isset($_GET["yr"])) { $modulereadyear=explode(",", $_GET["yr"]); }
if(isset($_GET["sem"])) { $modulereadsem=explode(",", $_GET["sem"]); }

$output = "";
ob_start();
$strA = "";
$strB = "";
$index = 0;
foreach($modulecode as $mod) {
	$strA .= " WHEN '{$mod}' THEN '{$modulereadyear[$index]}'";
	$strB .= " WHEN '{$mod}' THEN '{$modulereadsem[$index]}'";
	$index ++;
}

$query = $wpdb->prepare("UPDATE $wpdb->moduledata SET $wpdb->moduledata.year = CASE $wpdb->moduledata.modulecode{$strA} ELSE $wpdb->moduledata.year END, $wpdb->moduledata.sem = CASE $wpdb->moduledata.modulecode{$strB} ELSE $wpdb->moduledata.sem END WHERE $wpdb->moduledata.username='{$username}'");
/*$query = $wpdb->prepare("UPDATE $wpdb->moduledata 
	SET $wpdb->moduledata.year = CASE $wpdb->moduledata.modulecode 
		WHEN 'MA1101R' THEN '1' 
	END, 
	$wpdb->moduledata.sem = CASE $wpdb->moduledata.modulecode 
		WHEN 'MA1101R' THEN '1' 
	END 
WHERE $wpdb->moduledata.username='admin'"
);
*/
$result = $wpdb->query( $query );

ob_end_clean();


if ($result == $index) {
	echo 1;
} else if ($result == 0) {
	echo 2;
} else {
	echo 3;
}
?>
