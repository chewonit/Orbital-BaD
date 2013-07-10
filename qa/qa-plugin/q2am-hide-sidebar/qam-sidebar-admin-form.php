<?php
/*
*	Q2AM Hide Sidebar
*	
*	Adds element to the template file
*	
*	@author			Q2A Market
*	@category		Plugin
*	@Version 		1.1
*	@URL			http://www.q2amarket.com
*	
*	@Q2A Version	1.5.2
*
*	Modify this file as per your need, especially if you need to change css
*/

class qam_sidebar_admin_form
{

	function admin_form(&$qa_content)
	{
		$saved=false;
		
		if (qa_clicked('sidebar_save_button')) {

			qa_opt('sidebar_qa', (bool)qa_post_text('sidebar_qa'));
			qa_opt('sidebar_activity', (bool)qa_post_text('sidebar_activity'));
			qa_opt('sidebar_questions', (bool)qa_post_text('sidebar_questions'));
			qa_opt('sidebar_question', (bool)qa_post_text('sidebar_question'));
			qa_opt('sidebar_hot', (bool)qa_post_text('sidebar_hot'));
			qa_opt('sidebar_unanswered', (bool)qa_post_text('sidebar_unanswered'));
			qa_opt('sidebar_tags', (bool)qa_post_text('sidebar_tags'));
			qa_opt('sidebar_categories', (bool)qa_post_text('sidebar_categories'));
			qa_opt('sidebar_users', (bool)qa_post_text('sidebar_users'));
			qa_opt('sidebar_admin', (bool)qa_post_text('sidebar_admin'));
			qa_opt('sidebar_custom', (bool)qa_post_text('sidebar_custom'));
			qa_opt('sidebar_ask', (bool)qa_post_text('sidebar_ask'));

			$saved=true;
		}
		
		return array(
			'ok' => $saved ? 'Q2AM Sidebar settings saved' : null,
			
			'fields' => array(

				array(
					'label' => 'Home',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_qa'),
					'tags' => 'NAME="sidebar_qa" ID="sidebar_qa"',
				),

				array(
					'label' => 'All Activity',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_activity'),
					'tags' => 'NAME="sidebar_activity" ID="sidebar_activity"',
				),

				array(
					'label' => 'Questions',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_questions'),
					'tags' => 'NAME="sidebar_questions" ID="sidebar_questions"',
				),

				array(
					'label' => 'Question',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_question'),
					'tags' => 'NAME="sidebar_question" ID="sidebar_question"',
				),

				array(
					'label' => 'Hot',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_hot'),
					'tags' => 'NAME="sidebar_hot" ID="sidebar_hot"',
				),

				array(
					'label' => 'Unanswered',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_unanswered'),
					'tags' => 'NAME="sidebar_unanswered" ID="sidebar_unanswered"',
				),

				array(
					'label' => 'Tags',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_tags'),
					'tags' => 'NAME="sidebar_tags" ID="sidebar_tags"',
				),

				array(
					'label' => 'Categories',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_categories'),
					'tags' => 'NAME="sidebar_categories" ID="sidebar_categories"',
				),

				array(
					'label' => 'Users',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_users'),
					'tags' => 'NAME="sidebar_users" ID="sidebar_users"',
				),

				array(
					'label' => 'Admin',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_admin'),
					'tags' => 'NAME="sidebar_admin" ID="sidebar_admin"',
				),

				array(
					'label' => 'Custom',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_custom'),
					'tags' => 'NAME="sidebar_custom" ID="sidebar_custom"',
				),

				array(
					'label' => 'Ask a Question',
					'type' => 'checkbox',
					'value' => qa_opt('sidebar_ask'),
					'tags' => 'NAME="sidebar_ask" ID="sidebar_ask"',
				),
				
			),
			
			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'NAME="sidebar_save_button"',
				),
			),
		);
	}

}

/*
	Omit PHP closing tag to help avoid accidental output
*/	