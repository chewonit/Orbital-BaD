<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div class="faq_approve">
	<p><?php _e ('Rating', 'faqtastic'); ?>: <?php echo $question->approval (); ?></p>

	<a href="http://some-site.com/">
		<img align="top" src="<?php echo $this->url () ?>/images/positive.png" width="14" height="14" alt="<?php _e ('Positive', 'faqtastic'); ?>"/>
	</a>
	<a href="http://some-site.com/">
		<img align="top" src="<?php echo $this->url () ?>/images/negative.png" width="14" height="14" alt="<?php _e ('Negative', 'faqtastic'); ?>"/>
	</a>
</div>