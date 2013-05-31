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
$output = '<div style="color:red">Account flushed and course module template structure loaded in</div>';
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
ob_end_clean();
/*
function my_ofset($text){
    preg_match('/^\D*(?=\d)/', $text, $m);
    return strlen($m[0]);
}
if($operation == "checkModule") { 
	if($checkValue) {
		$wpdb->update( 
			'wp_moduledata',
			array(
				'isTaken' => $checkValue
			),
			array( 'id' => $id ),
			array( 
				'%d'
			), 
			array( '%d' ) 
		);
		$output = $id . ",istaken"; // Module has been marked as Taken.
		
		$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.id='{$id}'" );
		$modulecodeArr = $wpdb->get_results( $query );
		$modulecode = $modulecodeArr[0]->modulecode;
		$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.modulepreq REGEXP '{$modulecode}'" );
		$rawmodule = $wpdb->get_results( $query );
		
		foreach($rawmodule as $a) {
			//echo $a->modulecode;
			$flag = true;
			$arr = explode(",", $a->modulepreq);
			foreach($arr as $module) {
				if (!($wpdb->get_var( "SELECT $wpdb->moduledata.istaken FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$module}' AND $wpdb->moduledata.username='{$username}'" ))) {
					$flag = false;
					break;
				}
			}
			// This module is now unlocked
			if($flag) {
				$wpdb->update( 
					'wp_moduledata',
					array(
						'status' => 'available'
					),
					array( 'id' => $a->id ),
					array( 
						'%s'
					), 
					array( '%d' ) 
				);
				$output = $output . "," . $a->id . ",available";
			}
		}
	} else {
		$flag = true;
		$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.id='{$id}'" );
		$modulecodeArr = $wpdb->get_results( $query );
		$modulecode = $modulecodeArr[0]->modulecode;
		$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.modulepreq REGEXP '{$modulecode}'" );
		$rawmodule = $wpdb->get_results( $query );
		
		foreach($rawmodule as $a) {
			// This array contains the modules that has the target module as prerequisite
			if ($a->istaken) {
				$flag = false;
				break;
			}
		}
		if($flag) {
			$wpdb->update( 
				'wp_moduledata',
				array(
					'isTaken' => $checkValue
				),
				array( 'id' => $id ),
				array( 
					'%d'
				), 
				array( '%d' ) 
			);
		
			$output = $id . ",available"; // Module has been marked as Not Taken.
			
			foreach($rawmodule as $a) {
				$wpdb->update( 
					'wp_moduledata',
					array(
						'status' => 'locked'
					),
					array( 'id' => $a->id ),
					array( 
						'%s'
					), 
					array( '%d' ) 
				);
				$output = $output . "," . $a->id . ",locked";
			}
		} else {
			$output = "1=" . $id;
		}
	}
	
	echo $output;
	return;
}
if($operation == "delete") { 
	$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.id='{$id}'" );
	$modulecodeArr = $wpdb->get_results( $query );
	$modulecode = $modulecodeArr[0]->modulecode;
	$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.modulepreq REGEXP '{$modulecode}'" );
	$rawmodule = $wpdb->get_results( $query );
	if(count($rawmodule) == 0) {
		$wpdb->query( 
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->moduledata
				WHERE id = %d
				",
					$id
			)
		);
		$output = '<div style="color:red">Module Deleted.</div>';
	} else {
		$output = '<div style="color:red">';
		for ($i = 0; $i < count($rawmodule); $i++) {
			if($i == (count($rawmodule)-1)) {
				$output = $output . $rawmodule[$i]->modulecode . " ";
				break;
			}
			$output = $output . $rawmodule[$i]->modulecode . ", ";
		}
		$output = $output . "has " . $modulecode . " as a prerequisite!<br />Unable to delete module.</div>";
	}
}
if($operation == "insert") {
	$level = $modulecode[my_ofset($modulecode)];
	$dupe = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$modulecode}' AND $wpdb->moduledata.username='{$username}'" );
	$missingpreq = "noModule";
	$status = "available";
	if($modulepreq == null) {
		$status="available";
	} else {		
		$arr = explode(",", $modulepreq);
		foreach($arr as $module) {
			if ($wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$module}' AND $wpdb->moduledata.username='{$username}'" ) == "0") {
				// Prerequisite Not in the user module pool
				$missingpreq = $module;
				break;
			} else {
				if (!($wpdb->get_var( "SELECT $wpdb->moduledata.istaken FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$module}' AND $wpdb->moduledata.username='{$username}'" ))) {
					// if prerequisite module is not taken yet
					$status = "locked";
					break;
				}
			}
		}
	}
	if($dupe == 0 && $missingpreq=="noModule") {
		$wpdb->insert( 
			'wp_moduledata', 
			array( 
				'username' => $username, 
				'modulecode' => strtoupper($modulecode),
				'modulename' => ucwords(strtolower($modulename)),
				'modulepreq' => strtoupper($modulepreq),
				'level' => $level,
				'status' => $status
			), 
			array( 
				'%s', 
				'%s',
				'%s',
				'%s',
				'%d',
				'%s'
			) 
		);
		$output = '<div style="color:red">Module Added.</div>';
	} else if($missingpreq != "noModule") {
		$output = '<div style="color:red">Cannot find prerequisite module!</div><div style="color:red">Create module '. $missingpreq .' first!</div>';
	} else {
		$output = '<div style="color:red">A entry with the same Module Code already exist!</div><div style="color:red">No changes mande</div>';
	}
}
if($operation == "update") {

	$modulecodeflag = false;
	$missingpreq = "noModule";
	$status = "available";
	
	$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.id='{$id}'" );
	$modulecodeArr = $wpdb->get_results( $query );
	$originalmodulecode = $modulecodeArr[0]->modulecode;
	
	if(strcasecmp($originalmodulecode, $modulecode)!=0) {
		// Module code has been changed
		// Check if there exist and module containing this as a prerequisite
		$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.modulepreq REGEXP '{$originalmodulecode}'" );
		$rawmodule = $wpdb->get_results( $query );
		
		if(count($rawmodule) != 0) {
			$output = '<div style="color:red">';
			for ($i = 0; $i < count($rawmodule); $i++) {
				if($i == (count($rawmodule)-1)) {
					$output = $output . $rawmodule[$i]->modulecode . " ";
					break;
				}
				$output = $output . $rawmodule[$i]->modulecode . ", ";
			}
			$output = $output . "has " . $originalmodulecode . " as a prerequisite!<br />Unable to edit module.</div>";
			$modulecodeflag = false;
		} else {
			// There is no module containing this as a prerequisite
			// Safe to cahnge module name
			$modulecodeflag = true;
		}
	} else {
		// Module code has NOT been changed
		$modulecodeflag = true;
	}
	
	if($modulecodeflag) {
		if($modulepreq != null) {
			$arr = explode(",", $modulepreq);
			foreach($arr as $module) {
				if ($wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$module}' AND $wpdb->moduledata.username='{$username}'" ) == "0") {
					// Prerequisite Not in the user module pool
					$missingpreq = $module;
					break;
				} else {
					if (!($wpdb->get_var( "SELECT $wpdb->moduledata.istaken FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$module}' AND $wpdb->moduledata.username='{$username}'" ))) {
						// if prerequisite module is not taken yet
						$status = "locked";
						break;
					}
				}
			}
		} else {
			$status="available";
		}
		
		$level = $modulecode[my_ofset($modulecode)];
		$dupe = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$modulecode}' AND $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.id !={$id}" );
		if($dupe == 0 && $missingpreq=="noModule") {
			$wpdb->update( 
				'wp_moduledata',
				array(
					'modulecode' => $modulecode,
					'modulename' => ucwords(strtolower($modulename)),
					'modulepreq' => strtoupper($modulepreq),
					'level' => $level,
					'status' => $status
				),
				array( 'id' => $id ),
				array( 
					'%s',
					'%s',
					'%s',
					'%d',
					'%s'
				), 
				array( '%d' ) 
			);
			$output = '<div style="color:red">Module Updated.</div>';
		} else if($missingpreq != "noModule") {
			$output = '<div style="color:red">Cannot find prerequisite module!</div><div style="color:red">Create module '. $missingpreq .' first!</div>';
		} else {
			$output = '<div style="color:red">A entry with the same Module Code already exist!</div><div style="color:red">No changes mande</div>';
		}
	}
}
ob_end_clean();
*/
echo $output;

echo '<div id="module-list">';

// count the modules
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );
echo "<p>Total Module Count: {$user_count}</p>";

// Retreive all modules tagged with this user
$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' ORDER BY $wpdb->moduledata.modulecode" );
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
	print_r('<div style="font-weight:bold;">' . $a->modulecode . ": " . $a->modulename . "</div>");
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
	
	echo '<div class="edit-box" id="edit' . $a->id . '" modid="' . $a->id . '" style="margin: 10px; background-color:white; display:none;">'
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
		.'<input id="modulepreq_txt' . $a->id . '" type="text" name="modulepreq_txt' . $a->id . '" value="' . $a->modulepreq . '" style="display:none;">'
		.'<div style="float:left; margin: 0 3px;"><input class="editaddpreqbtn" modid="' . $a->id . '" id="editaddPreq' . $a->id . '" type="button" value="Add Prerequisite"></div>'
		.'<div id="preq-list' . $a->id . '" class="preq-list" style="float:left; margin: 0 3px;"></div>'
		.'</div></div>'
		.'<input id="updateid_txt' . $a->id . '" type="text" name="updateid_txt' . $a->id . '" style="display:none" value="' . $a->id . '">'
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
