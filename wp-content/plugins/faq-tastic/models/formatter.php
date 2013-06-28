<?php

class Formatter
{
	function display ($text)
	{
		global $faqtastic;
		static $options;
		static $textile;
		
    if (!isset ($options))
		{
			$options = $faqtastic->get_options ();
			if ($options['textile'])
				include (dirname (__FILE__).'/textile.php');
		}

		if ($options['textile'])
			echo textile ($text);
		else
			echo wpautop ($text);
	}
}

?>