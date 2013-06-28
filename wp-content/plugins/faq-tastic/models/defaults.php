<?php

class FAQDefaults
{
	function ask ()
	{
		global $faqtastic;
		return get_option ('faq_button') ? get_option ('faq_button') : __ ('Ask Question', 'faqtastic');
	}
	
	function approved_simple ($question = '', $message = '')
	{
		global $faqtastic;
		$msg = get_option ('faq_approved_simple') ? get_option ('faq_approved_simple') : $faqtastic->capture ('approved_simple');
		if ($question)
			return FAQDefaults::replace ($msg, $question, $message);
		return $msg;
	}
	
	function approved_paged ($question = '', $message = '')
	{
		global $faqtastic;
		$msg = get_option ('faq_approved_paged') ? get_option ('faq_approved_paged') : $faqtastic->capture ('approved_paged');
		if ($question)
			return FAQDefaults::replace ($msg, $question, $message);
		return $msg;
	}
	
	function thanks ($question = '')
	{
		global $faqtastic;
		$msg = get_option ('faq_thanks') ? get_option ('faq_thanks') : $faqtastic->capture ('thanks');
		if ($question)
			return FAQDefaults::replace ($msg, $question);
		return $msg;
	}
	
	function failed ()
	{
		global $faqtastic;
		return get_option ('faq_failed') ? get_option ('faq_failed') : $faqtastic->capture ('failed');
	}
	
	function rating ($question = '')
	{
		$msg = get_option ('faq_rating') ? get_option ('faq_rating') : __ ('Thanks for your vote!', 'faqtastic');
		if ($question)
			return FAQDefaults::replace ($msg, $question);
		return $msg;
	}
	
	function rejected ($question = '', $message = '')
	{
		global $faqtastic;
		$msg = get_option ('faq_rejected') ? get_option ('faq_rejected') : $faqtastic->capture ('rejected');
		if ($question)
			return FAQDefaults::replace ($msg, $question, $message);
		return $msg;
	}
	
	function replace ($text, $question, $message = '')
	{
		$text = str_replace ('$question$', $question->question, $text);
		$text = str_replace ('$answer$',   $question->answer, $text);
		
		if ($question->page_id == 0)
			$text = str_replace ('$page$', '', $text);
		else
			$text = str_replace ('$page$', get_permalink ($question->page_id), $text);
			
		$text = str_replace ('$message$', $message, $text);
		return $text;
	}
	
	function admin_email ($question)
	{
		global $faqtastic;
		return $faqtastic->capture_admin ('admin_email', array ('question' => $question));
	}
}

?>