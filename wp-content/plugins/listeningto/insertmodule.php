

Welcome <?php echo $_REQUEST["modulecode_txt"]; ?>!<br>
You are <?php echo $_REQUEST["modulename_txt"]; ?> years old.

<?php

global $current_user;
get_currentuserinfo();
$username = $current_user->user_login;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
									
$wpdb->insert( 
	'moduledata', 
	array( 
		'username' => 'testuser', 
		'modulecode' => '123',
		'modulename' => 'name of module'
	), 
	array( 
		'%s', 
		'%s',
		'%s'
	) 
);
?>