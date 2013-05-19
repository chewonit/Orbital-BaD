<?php
/*
Template Name: Module Functions
*/
?>
<?php
global $wpdb;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
// Check is there is a valid operation, else redirect to error 404
if(isset($_GET["op"])) { $operation=$_GET["op"]; }
	else { wp_redirect( get_home_url() ."/error" ); exit;}
if(isset($_GET["ur"])) { $username=$_GET["ur"]; }
if(isset($_GET["mc"])) { $modulecode=$_GET["mc"]; }
if(isset($_GET["mn"])) { $modulename=$_GET["mn"]; }
if(isset($_GET["preq"])) { $modulepreq=$_GET["preq"]; }
if(isset($_GET["id"])) { $id=$_GET["id"]; }
$modulecode = strtoupper($modulecode);
$output = "";
ob_start();
function my_ofset($text){
    preg_match('/^\D*(?=\d)/', $text, $m);
    return strlen($m[0]);
}
if($operation == "delete") { 
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
}
if($operation == "insert") {
	$level = $modulecode[my_ofset($modulecode)];
	$dupe = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$modulecode}' AND $wpdb->moduledata.username='{$username}'" );
	if($dupe == 0) {
		$wpdb->insert( 
			'wp_moduledata', 
			array( 
				'username' => $username, 
				'modulecode' => strtoupper($modulecode),
				'modulename' => ucwords(strtolower($modulename)),
				'modulepreq' => strtoupper($modulepreq),
				'level' => $level
			), 
			array( 
				'%s', 
				'%s',
				'%s',
				'%s',
				'%d'
			) 
		);
		$output = '<div style="color:red">Module Added.</div>';
	} else {
		$output = '<div style="color:red">A entry with the same Module Code already exist!</div><div style="color:red">No changes mande</div>';
	}
}
if($operation == "update") {
	$level = $modulecode[my_ofset($modulecode)];
	$dupe = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$modulecode}' AND $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.id !={$id}" );
	if($dupe == 0) {
		$wpdb->update( 
			'wp_moduledata',
			array(
				'modulecode' => $modulecode,
				'modulename' => ucwords(strtolower($modulename)),
				'modulepreq' => strtoupper($modulepreq),
				'level' => $level
			),
			array( 'id' => $id ),
			array( 
				'%s',
				'%s',
				'%s',
				'%d'
			), 
			array( '%d' ) 
		);
		$output = '<div style="color:red">Module Updated.</div>';
	} else {
		$output = '<div style="color:red">A entry with the same Module Code already exist!</div><div style="color:red">No changes mande</div>';
	}
}
ob_end_clean();

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
	
	echo '<div class="module-item"><div>';
	print_r('<div style="float:left">' . $a->modulecode . ": " . $a->modulename . "</div>");
	
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
	
	echo '</div>';
	// -- End of Manage module buttons container

	echo "<br style='clear:both;'/></div>";
	
	// More module details
	echo '<div style="float:left; margin-right:10px;">Level: '. $a->level .'000</div>'
		.'<div style="float:left">Prerequisite: '. $preq . '</div>';
	echo '<div style="clear:both;"></div>';
	
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
