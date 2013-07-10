<?php
/*
Template Name: Flush Modules
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
if(isset($_GET["course"])) { $course=$_GET["course"]; }

$courseArray = array(
	"cmrequirements" => "Communications and Media",
    "csrequirements" => "Computer Science",
	"isrequirements" => "Information Systems",
	"ecrequirements" => "Electronic Commerce",
);

$output = '<div style="color:red">Account flushed and '.$courseArray[$course].' module template structure loaded in.<br />'
	.'Please note that only core modules have been added in.<br />'
	.'Project, electives and modules with options (i.e. Science Modules) have been omitted.</div>';
ob_start();
$wpdb->query( 
	$wpdb->prepare( 
		"
		DELETE FROM $wpdb->moduledata WHERE username = %s;
		",
			$username
	)
);
$wpdb->query( 
	$wpdb->prepare( 
		"
		INSERT INTO `wp_moduledata`(`username`, `modulecode`, `modulename`, `modulepreq`, `level`, `status`, `istaken`, `mc`)
		SELECT %s, `modulecode`, `modulename`, `modulepreq`, `level`, `status`, `istaken`, `mc` FROM `wp_moduledata`
		WHERE `username`=%s
		",
			$username,
			$course
	)
);
// get user preference for module order
if (!isset($wpdb->usermoduledata)) {
	$wpdb->usermoduledata = $table_prefix . 'usermoduledata';
}
$check_pref_exist = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->usermoduledata WHERE $wpdb->usermoduledata.username='{$username}'" );
if($check_pref_exist == 0){
	// User Preference not found
	// Create new entry
	$wpdb->insert( 
		'wp_usermoduledata', 
		array( 
			'username' => $username
		), 
		array( 
			'%s'
		) 
	);
} else {
	// Get module order preference
	$query = $wpdb->prepare( "SELECT $wpdb->usermoduledata.moduleorder FROM $wpdb->usermoduledata WHERE $wpdb->usermoduledata.username='{$username}' ");
	$rawresults = $wpdb->get_results( $query );
	foreach($rawresults as $a) {
		$moduleorder = $a->moduleorder;
	}
}
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
ob_end_clean();

echo $output;

echo '<div id="module-list">';

// Retreive all modules tagged with this user
$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' ORDER BY $wpdb->moduledata.{$moduleorder}" );
$rawmodule = $wpdb->get_results( $query );

// populate the modules
foreach($rawmodule as $a) {
	if($a->modulepreq==null) {
		$preq = "nill";
	} else {
		$preq = $a->modulepreq;
	}
										
	if($a->istaken) {
		echo '<div id="module-item'. $a->id .'" class="module-item module-istaken"><div>';
	} else if($a->status=="available"){
		echo '<div id="module-item'. $a->id .'" class="module-item module-available"><div>';
	} else {
		echo '<div id="module-item'. $a->id .'" class="module-item"><div>';
	}
	echo '<div style="float:left">';
	print_r('<div style="font-weight:bold;"><a href="Javascript:modulepopup(\''. $a->modulecode .'\')">' 
		. $a->modulecode . ": " . $a->modulename . "</a></div>");
	// More module details
	echo '<div style="float:left; margin-right:10px;">Level: '. $a->level .'000</div>'
		.'<div style="float:left">Prerequisite: '. $preq . '</div>';
	echo '<div style="clear:both;"></div>';
	echo '</div>';
	
	echo '<hr class="module-item-controls-bar" />';
	
	// -- Manage module buttons container
	echo '<div style="float:right">';
	
	// Module Delete button
	echo '<div style="float:right">'
		.'<form id="deleteform' . $a->id . '" action="JavaScript:deleteMod('. $a->id .')">'
		//.'<form name="deleteform' . $a->id . '" action="' . get_permalink() . '" method="get">'
		.'<input id="id_txt' . $a->id . '" type="text" name="id_txt" style="display:none;" value="' . $a->id . '">'
		.'<input id="deletebutton" type="submit" value="Delete" style="display:inline">'
		.'</form></div>';
		
	// Module Edit button
	echo '<div style="float:right">'
		.'<form id="editform' . $a->id . '" action="JavaScript:editMod('. $a->id .')">'
		.'<input id="editid_txt" type="text" name="editid_txt" style="display:none;" value="' . $a->id . '">'
		.'<input id="editbutton" type="submit" value="Edit" style="display:inline">'
		.'</form></div>';
		
	// Module Taken button
	echo '<div style="float:right">'
		.'<form id="takenform' . $a->id . '" action="JavaScript:alert('. $a->id .')">'
		.'<input id="takenid_txt" type="text" name="takenid_txt" style="display:none;" value="' . $a->id . '">';
	if($a->istaken) {
		echo 'Module Taken: <input id="takencheckbox' . $a->id . '" modid="' . $a->id . '" class="modulecheckbox" type="checkbox" value="Module Taken" style="display:inline" checked>&nbsp;';
	} else if($a->status=="available"){
		echo 'Module Taken: <input id="takencheckbox' . $a->id . '" modid="' . $a->id . '" class="modulecheckbox" type="checkbox" value="Module Taken" style="display:inline">&nbsp;';
	} else {
		echo 'Module Taken: <input id="takencheckbox' . $a->id . '" modid="' . $a->id . '" class="modulecheckbox" type="checkbox" value="Module Taken" style="display:inline" disabled="true">&nbsp;';
	}
	echo '</form></div>';
	
	echo '</div>';
	// -- End of Manage module buttons container
									
	echo "<br style='clear:both;'/></div>";
	
	echo '<div class="edit-box" id="edit' . $a->id . '" modid="' . $a->id . '">'
		.'<form name="input" action="'. get_permalink() .'" method="get">'
		.'<div>'
		.'<div class="input-module"><div>Module code: </div><div><input id="editmodulecode_txt' . $a->id . '" type="text" name="editmodulecode_txt' . $a->id . '" value="' . $a->modulecode . '"></div></div>'
		.'<div class="input-module"><div>Module name: </div><div><input id="editmodulename_txt' . $a->id . '" type="text" name="editmodulename_txt' . $a->id . '" value="' . $a->modulename . '"></div></div>'
		.'</div>'
		.'<br style="clear:both;" />'
		.'<div>'
		.'<div class="input-module"><div>Module Prerequisite: </div>'
		.'<div>'
		.'<div style="float:left;"><input id="modulepreq2_txt' . $a->id . '" type="text" name="modulepreq2_txt' . $a->id . '"></div>'
		.'<input id="modulepreq_txt' . $a->id . '" type="text" name="modulepreq_txt' . $a->id . '" value="' . $a->modulepreq . '" style="display:none">'
		.'<div style="float:left; margin: 0 3px;"><input class="editaddpreqbtn" modid="' . $a->id . '" id="editaddPreq' . $a->id . '" type="button" value="Add Prerequisite"></div>'
		.'<div id="preq-list' . $a->id . '" class="preq-list" style="float:left; margin: 0 3px;"></div>'
		.'</div></div>'
		.'<br style="clear:both;" />'
		.'<input id="updateid_txt' . $a->id . '" type="text" name="updateid_txt' . $a->id . '" style="display:none" value="' . $a->id . '">'
		.'</div>'
		.'<div style="margin-top: 8px;">'
		.'<div class="input-module">'
		.'<label for="editmodulecredit_txt' . $a->id . '">Module Credits: </label>';
		
	$temparray = array_fill(0, 12, '');
	$temparray[$a->mc] = "selected";
		
	echo '<select id="editmodulecredit_txt' . $a->id . '">
			<option value="0" '.$temparray[0].'>0</option>
			<option value="1" '.$temparray[1].'>1</option>
			<option value="2" '.$temparray[2].'>2</option>
			<option value="3" '.$temparray[3].'>3</option>
			<option value="4" '.$temparray[4].'>4</option>
			<option value="5" '.$temparray[5].'>5</option>
			<option value="6" '.$temparray[6].'>6</option>
			<option value="8" '.$temparray[8].'>8</option>
			<option value="12" '.$temparray[12].'>12</option>
		</select>'
		.'</div>'
		.'<div class="input-module">';
	
	$temparray = array_fill(0, 11, '');
	$temparray[$a->grade] = "selected";
	
	echo '<label for="editmodulegrade_txt' . $a->id . '">Grade: </label>
		<select id="editmodulegrade_txt' . $a->id . '">
			<option value="0" '.$temparray[0].'>N/A</option>
			<option value="1" '.$temparray[1].'>A+</option>
			<option value="2" '.$temparray[2].'>A</option>
			<option value="3" '.$temparray[3].'>A-</option>
			<option value="4" '.$temparray[4].'>B+</option>
			<option value="5" '.$temparray[5].'>B</option>
			<option value="6" '.$temparray[6].'>B-</option>
			<option value="7" '.$temparray[7].'>C+</option>
			<option value="8" '.$temparray[8].'>C</option>
			<option value="9" '.$temparray[9].'>D+</option>
			<option value="10" '.$temparray[10].'>D</option>
			<option value="11" '.$temparray[11].'>F</option>
		</select>'
		.'</div>'
		.'</div>'
		.'<br style="clear:both;" />'
		
		.'<div style="margin-top: 8px;">'
		.'<div class="input-module">'
		.'<label for="editmodulereadyear_txt' . $a->id . '">Read module in: Year </label>';
		
	$temparray = array_fill(0, 4, '');
	$temparray[$a->year] = "selected";
		
	echo '<select id="editmodulereadyear_txt' . $a->id . '">
			<option value="0" '.$temparray[0].'>N/A</option>
			<option value="1" '.$temparray[1].'>1</option>
			<option value="2" '.$temparray[2].'>2</option>
			<option value="3" '.$temparray[3].'>3</option>
			<option value="4" '.$temparray[4].'>4</option>
		</select>'
		.'</div>'
		.'<div class="input-module">';
	
	$temparray = array_fill(1, 2, '');
	$temparray[$a->sem] = "selected";
	
	echo '<label for="editmodulereadsem_txt' . $a->id . '">Semester: </label>
		<select id="editmodulereadsem_txt' . $a->id . '">
			<option value="1" '.$temparray[1].'>1</option>
			<option value="2" '.$temparray[2].'>2</option>
		</select>'
		.'</div>'
		.'</div>'
		.'<br style="clear:both;" />'
		
		.'<div class="input-module-submit"><input modid="' . $a->id . '" class="editmodulebtn" id="editmodulebtn' . $a->id . '" type="button" value="Update"></div></form>'
		.'</div>';
	
	// -- End of module item
	echo '</div>';
}
// -- End of module-list
echo '</div>';
?>
