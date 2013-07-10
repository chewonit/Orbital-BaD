<?php
/*
Template Name: Module Count
*/
?>
<?php
global $wpdb;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
// Check is there is a valid operation, else redirect to error 404
if(isset($_GET["ur"])) { $username=$_GET["ur"]; }
	else { wp_redirect( get_home_url() ."/error" ); exit;}

// count the modules
$mod_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );
$lvl_count = $wpdb->get_var( "SELECT SUM($wpdb->moduledata.mc) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.level='1'" );
$mc_count = $wpdb->get_var( "SELECT SUM($wpdb->moduledata.mc) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );

// Calculate CAP 
$query = $wpdb->prepare( "SELECT $wpdb->moduledata.mc, $wpdb->moduledata.grade FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.mc!=0 AND $wpdb->moduledata.grade!=0");
$rawmodule = $wpdb->get_results( $query );
$totalmccap = 0.00; $tempsum = 0.00; $cumulativecap = number_format((float)0.00, 2, '.', '');
$gradearray = array(0, 5.0, 4.5, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0);
foreach($rawmodule as $a) {
	$totalmccap += $a->mc;
	$tempsum += ($a->mc)*($gradearray[$a->grade]);
}
if($tempsum > 0) {$cumulativecap = number_format((float)$tempsum/$totalmccap, 2, '.', '');}

echo "<p>Total Module Count: {$mod_count}<br />Level 1 MC Count: {$lvl_count}<br />Total MC Count: {$mc_count}<br />Cumulative CAP: {$cumulativecap}</p>";
?>
