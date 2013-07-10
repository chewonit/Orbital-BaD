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

$gradearray = array('N/A', 'A+','A','A-','B+','B','B-','C+','C','D+','D','F');

foreach($rawmodule as $a) {
	if($a->istaken) {
		$statusremark = $a->modulecode . " has already been cleared.";
		$classstyle = "";
	} else {
		switch ($a->status) {
			case "available":
				$statusremark = $a->modulecode . " is available to be taken.";
				$classstyle = "module-available";
				break;
			case "locked":
				$statusremark = "Prerequisites have not been cleared.";
				$classstyle = "module-locked";
				break;
		}
	}
		
	$output .= '<div class="popup-module-container '.$classstyle.'"><div><strong>' . $a->modulecode . ': ' . $a->modulename . '</strong></div>'
		.'<div>Status: ' . $statusremark . '</div>'
		.'<div>level: ' . $a->level . '000</div>'
		.'<div>MC: ' . $a->mc . '</div>'
		.'<div>Grade: ' . $gradearray[$a->grade] . '</div>';
		
	if($a->year != 0) {
		$output .= '<div>Read module in: Year ' . $a->year . ' Sem ' . $a->sem . '</div>';
	}
			
	$output .= '</div>';
	$output .= '<div class="popup-module-container"><div><strong>Prerequisites</strong></div>';

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
				if($b->istaken) {
					$statusremark = $b->modulecode . " has already been cleared.";
					$classstyle = "";
				} else {
					switch ($b->status) {
						case "available":
							$statusremark = $b->modulecode . " is available to be taken.";
							$classstyle = "module-available";
							break;
						case "locked":
							$statusremark = "Prerequisites have not been cleared.";
							$classstyle = "module-locked";
							break;
					}
				}
			
				$output .= '<div class="popup-module2-container '.$classstyle.'"><div>' . $b->modulecode . ': ' . $b->modulename . '</div>'
					.'<div>Status: ' . $statusremark . '</div>'
					.'</div>';
			}
		}
	}
	$output .= "</div>";
}

$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulepreq REGEXP '{$modulecode}' AND $wpdb->moduledata.username='{$username}'" );
$rawmodule = $wpdb->get_results( $query );

$output .= '<div class="popup-module-container"><div><strong>Post requisites</strong></div>';

if($rawmodule == null) {
	$output .= "<div>nill</div>";
} else {
	foreach($rawmodule as $a) {
		if($a->istaken) {
			$statusremark = $a->modulecode . " has already been cleared.";
			$classstyle = "";
		} else {
			switch ($a->status) {
				case "available":
					$statusremark = $a->modulecode . " is available to be taken.";
					$classstyle = "module-available";
					break;
				case "locked":
					$statusremark = "Prerequisites have not been cleared.";
					$classstyle = "module-locked";
					break;
			}
		}
	
		$output .= '<div class="popup-module2-container '.$classstyle.'"><div>' . $a->modulecode . ': ' . $a->modulename . '</div>'
			.'<div>Status: ' . $statusremark . '</div>'
			.'</div>';
	}
}
$output .= "</div>";

echo $output;
?>
