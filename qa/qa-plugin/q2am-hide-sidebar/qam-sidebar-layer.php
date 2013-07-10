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

class qa_html_theme_layer extends qa_html_theme_base {

	function head_custom()
	{
		qa_html_theme_base:: head_custom();
		
		if(
			((qa_opt('sidebar_qa')) && ($this->template == 'qa') ||
			(qa_opt('sidebar_activity')) && ($this->template == 'activity') ||
			(qa_opt('sidebar_questions')) && ($this->template == 'questions') ||
			(qa_opt('sidebar_question')) && ($this->template == 'question') ||
			(qa_opt('sidebar_hot')) && ($this->template == 'hot') ||
			(qa_opt('sidebar_unanswered')) && ($this->template == 'unanswered') ||
			(qa_opt('sidebar_tags')) && ($this->template == 'tags') ||
			(qa_opt('sidebar_categories')) && ($this->template == 'categories') ||
			(qa_opt('sidebar_users')) && ($this->template == 'users') ||
			(qa_opt('sidebar_admin')) && ($this->template == 'admin') ||
			(qa_opt('sidebar_custom')) && ($this->template == 'custom') ||
			(qa_opt('sidebar_ask')) && ($this->template == 'ask')) && (qa_opt('site_theme') === 'Snow')
		)
			$this->output('<style type="text/css">
				.qa-main{
					width:100% !important;
					padding-right:10px;
					-moz-box-sizing:border-box;
					-webkit-box-sizing:border-box;
					box-sizing:border-box;
				}
				.qa-q-item-main{width:738px !important}
			</style>');
		
	}

	function sidepanel()
	{
		if(
			!(qa_opt('sidebar_qa')) && ($this->template == 'qa') ||
			!(qa_opt('sidebar_activity')) && ($this->template == 'activity') ||
			!(qa_opt('sidebar_questions')) && ($this->template == 'questions') ||
			!(qa_opt('sidebar_question')) && ($this->template == 'question') ||
			!(qa_opt('sidebar_hot')) && ($this->template == 'hot') ||
			!(qa_opt('sidebar_unanswered')) && ($this->template == 'unanswered') ||
			!(qa_opt('sidebar_tags')) && ($this->template == 'tags') ||
			!(qa_opt('sidebar_categories')) && ($this->template == 'categories') ||
			!(qa_opt('sidebar_users')) && ($this->template == 'users') ||
			!(qa_opt('sidebar_admin')) && ($this->template == 'admin') ||
			!(qa_opt('sidebar_custom')) && ($this->template == 'custom') ||
			!(qa_opt('sidebar_ask')) && ($this->template == 'ask')
		)		
			qa_html_theme_base::sidepanel();
	}

}



/*
	Omit PHP closing tag to help avoid accidental output
*/