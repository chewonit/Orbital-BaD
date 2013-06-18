<?php
/*
Template Name: Module Popup
*/
?>
<?php
global $wpdb;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
// Check is there is a valid operation, else redirect to error 404
if(isset($_GET["mc"])) { $modulecode=$_GET["mc"]; }
	else { wp_redirect( get_home_url() ."/error" ); exit;}
if(isset($_GET["ur"])) { $username=$_GET["ur"]; }
if(isset($_GET["mn"])) { $modulename=$_GET["mn"]; }
if(isset($_GET["preq"])) { $modulepreq=$_GET["preq"]; }
if(isset($_GET["id"])) { $id=$_GET["id"]; }
if(isset($_GET["cv"])) { $checkValue=$_GET["cv"]; }
if(isset($_GET["order"])) { $ordermoduleselection=$_GET["order"]; }
$modulecode = strtoupper($modulecode);
$output = '';

$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$modulecode}' AND $wpdb->moduledata.username='{$username}'" );
$rawmodule = $wpdb->get_results( $query );

foreach($rawmodule as $a) {
	$output .= '<div>' . $a->modulecode . ': ' . $a->modulename . '</div><br />';
	$output .= '<div>Prerequisites</div>';

	if($a->modulepreq==null) {
		$output .= "<div>nill</div>";
	} else {
		//$preq = explode(",", $a->modulepreq);
		$preq = $a->modulepreq;
		$preq = explode(",", $preq);
		$arrsize = count($preq);
		
		foreach($preq as $mod) {
			$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$mod}' AND $wpdb->moduledata.username='{$username}'" );
			$rawmod = $wpdb->get_results( $query );
			
			foreach($rawmod as $b) {
				$output .= '<div>' . $b->modulecode . ': ' . $b->modulename . '</div>';
			}
		}
		/*
		for ($i=0; $i<$arrsize; $i++) {
			if($i == $arrsize-1) {
				$output .= '<div>' . $preq[$i] . '</div>';
			} else {
				$output .= '<div>' . $preq[$i] . '</div>';
			}
		}
		*/
	}
}

$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulepreq REGEXP '{$modulecode}' AND $wpdb->moduledata.username='{$username}'" );
$rawmodule = $wpdb->get_results( $query );

$output .= '<br /><div>Post requisites</div>';

if($rawmodule == null) {
	$output .= "<div>nill</div>";
} else {
	foreach($rawmodule as $a) {
		$output .= '<div>' . $a->modulecode . ': ' . $a->modulename . '</div>';
	}
}

echo $output;
?>
