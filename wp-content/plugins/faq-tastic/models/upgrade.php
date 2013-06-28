<?php

class FT_Upgrade
{
	function run ($oldversion, $desired)
	{
		$this->install_database ();
		
		if ((get_option ('faq_thanks') !== false && $oldversion === false) || $oldversion == 0)
			$this->upgrade_from_0 ();
		else if ($oldversion == 2)
			$this->upgrade_from_2 ();
		else if ($oldversion == 3)
			$this->upgrade_from_3 ();
		
		update_option ('faq_version', $desired);
	}
	
	function install_database ()
	{
		global $wpdb;
		
		$wpdb->query ("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}faqtastic_groups` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `name` varchar(50) NOT NULL default '',
		  `page_id` int(10) unsigned NOT NULL default '0',
		  `ratings` int(10) unsigned NOT NULL default '1',
	  	`ask` varchar(40) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `name` (`name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		
		$wpdb->query ("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}faqtastic_questions` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `group_id` int(11) unsigned default NULL,
		  `page_id` int(11) unsigned NOT NULL default '0',
		  `created` datetime default NULL,
		  `position` int(10) unsigned NOT NULL default '0',
		  `status` enum('approved','pending') default 'pending',
		  `question` text,
		  `answer` text,
		  `positive` int(11) unsigned NOT NULL default '0',
		  `negative` int(11) unsigned NOT NULL default '0',
		  `notified` int(11) NOT NULL default '0',
			`author_email` varchar(50) default NULL,
		  `author_name` varchar(50) default NULL,
		  `author_website` varchar(50) default NULL,
		  `author_website_follow` tinyint(4) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		
		$wpdb->query ("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}faqtastic_related` (
		  `question_id` int(11) unsigned NOT NULL default '0',
		  `related_id` int(11) unsigned NOT NULL default '0',
		  `follow` int(11) NOT NULL default '1',
		  PRIMARY KEY  (`question_id`,`related_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	}
	
	function upgrade_from_3 ()
	{
		global $wpdb;
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_groups` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_related` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		$this->upgrade_from_0 ();
	}
	
	function upgrade_from_2 ()
	{
		$this->upgrade_from_3 ();
	}
	
	function upgrade_from_0 ()
	{
		global $wpdb;
		
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_groups` ADD `ask` varchar(40) default NULL;");
		
	  $wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` ADD `author_name` varchar(50) default NULL");
	  $wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` ADD `author_email` varchar(50) default NULL");
	  $wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` ADD `author_website` varchar(50) default NULL");
	  $wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` ADD `author_website_follow` tinyint(4) NOT NULL default '0'");

		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` CHANGE `created_by` `author_email` varchar(50) default NULL");
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_questions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_groups` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
		$wpdb->query ("ALTER TABLE `{$wpdb->prefix}faqtastic_related` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
	}
	
	function remove ($plugin)
	{
		global $wpdb;
		
		$wpdb->query ("DROP TABLE {$wpdb->prefix}faqtastic_groups");
		$wpdb->query ("DROP TABLE {$wpdb->prefix}faqtastic_questions");
		$wpdb->query ("DROP TABLE {$wpdb->prefix}faqtastic_related");
		
		delete_option ('faq_version');
		delete_option ('faq_approved_simple');
		delete_option ('faq_approved_paged');
		delete_option ('faq_thanks');
		delete_option ('faq_failed');
		delete_option ('faq_rejected');
		delete_option ('faq_rating');
		delete_option ('faq_options');

		// Deactivate the plugin
		$current = get_option('active_plugins');
		array_splice ($current, array_search (basename (dirname ($plugin)).'/'.basename ($plugin), $current), 1 );
		update_option('active_plugins', $current);
	}
}

?>