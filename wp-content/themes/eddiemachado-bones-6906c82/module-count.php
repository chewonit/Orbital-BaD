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
/*ob_start();
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
*/
// count the modules
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );
echo "<p>Total Module Count: {$user_count}</p>";
?>
