<?php
/*
Template Name: NUSmods
*/
?>
<?php
global $wpdb;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
// Check is there is a valid operation, else redirect to error 404
if(isset($_GET["ur"])) { $username=$_GET["ur"]; }
if(isset($_GET["mc"])) { $modulecode=$_GET["mc"]; }
if(isset($_GET["mn"])) { $modulename=$_GET["mn"]; }
if(isset($_GET["preq"])) { $modulepreq=$_GET["preq"]; }
if(isset($_GET["id"])) { $id=$_GET["id"]; }
if(isset($_GET["cv"])) { $checkValue=$_GET["cv"]; }
if(isset($_GET["order"])) { $ordermoduleselection=$_GET["order"]; }
if(isset($_GET["credit"])) { $modulecredit=$_GET["credit"]; }
	else { $modulecredit=0; }
if(isset($_GET["grade"])) { $modulegrade=$_GET["grade"]; }
	else { $modulegrade=0; }
$modulecode = strtoupper($modulecode);
$output = "";
ob_start();

// JSON NUSmods database	
$json = file_get_contents('http://nusmods.com/json/mod_info.json');
$nusmods = json_decode($json, true);

ob_end_clean();

echo "<info><title>".$nusmods['cors'][$modulecode]['title']."</title>"
	."<preq>".$nusmods['cors'][$modulecode]['prerequisite']."</preq>"
	."<mc>".$preq."</mc>"
	."<code>".$modulecode."</code>"
	."</info>";
?>
