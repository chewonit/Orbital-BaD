<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div class="faq_approve" id="fr_<?php echo $question->id ?>">
	<p><?php _e ('Rating', 'faqtastic'); ?>: <?php echo $question->approval (); ?></p>

	<?php if ($question->has_rated () == false) : ?>
	<a href="#" title="<?php _e ('This answer is good', 'faqtastic'); ?>" onclick="faq_rate(<?php echo $group->id ?>,<?php echo $question->id ?>,0); return false">
		<img align="top" src="<?php echo $this->url () ?>/images/positive.png" width="14" height="14" alt="<?php _e ('Positive', 'faqtastic'); ?>"/>
	</a>
	<a href="#" title="<?php _e ('This answer is bad', 'faqtastic'); ?>" onclick="faq_rate(<?php echo $group->id ?>,<?php echo $question->id ?>,1); return false">
		<img align="top" src="<?php echo $this->url () ?>/images/negative.png" width="14" height="14" alt="<?php _e ('Negative', 'faqtastic'); ?>"/>
	</a>
	<?php endif; ?>
</div>