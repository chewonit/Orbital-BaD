<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><link rel="stylesheet" href="<?php echo $css ?>/view/faqtastic/style.css" type="text/css" media="all" title="FAQ-Tastic style" charset="utf-8"/>
<script type="text/javascript" charset="utf-8" src="<?php echo $this->url () ?>/js/microajax.js"></script>
<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
	function faq_rate (item,item2,type)
	{
	  microAjax ('<?php echo $this->url () ?>/rate.php?item=' + item2 + '&grp=' + item + '&type=' + type, faq_after);
		document.getElementById ('fr_' + item2).style.display = 'none';
	}
	
	function faq_after (item) { alert (item); }
	/* ]]> */
</script>