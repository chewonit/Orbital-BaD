<?php

class FAQ_Features extends FAQTastic_Plugin
{
	var $lite;
	
	function FAQ_Features ($lite)
	{
		$this->lite = $lite;
		$this->register_plugin ('faqtastic', dirname (__FILE__));
	}

	function is_pro ()
	{
		return false;
	}
}

?>