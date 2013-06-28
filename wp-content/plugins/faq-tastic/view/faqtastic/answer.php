<div class="faq">
	<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><?php if ($allow_rating) : ?>
		<?php $this->render ('rating', array ('question' => $question, 'group' => $group)); ?>
	<?php endif; ?>

	<div class="answer">
		<?php Formatter::display ($question->answer) ?>
	</div>

	<?php if ($question->author_name) : ?>
	<div class="author">
		<?php _e ('Asked by', 'faqtastic'); ?>: <?php echo $question->author_link (); ?>
	</div>
	<?php endif; ?>

	<?php $this->render ('related', array ('related' => $question->get_related ())); ?>
</div>