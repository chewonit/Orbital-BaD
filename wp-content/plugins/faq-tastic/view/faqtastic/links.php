<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><?php if (!empty ($questions)) :?>
	<ol class="faq">
		<?php foreach ($questions AS $pos => $question) : ?>
		<li<?php if ($pos % 2 == 1) echo ' class="alt"' ?>>
			<?php if ($allow_rating && ($question->positive > 0 || $question->negative > 0)) : ?>
			 <div class="faq_approve"><p><?php _e ('Rating', 'faqtastic'); ?>: <?php echo $question->approval (); ?></p></div>
			<?php endif; ?>
				<a href="<?php echo get_permalink ($question->page_id); ?>"><?php echo $question->question; ?></a>
		</li>
		<?php endforeach; ?>
	</ol>
	
<?php else : ?>
  <p><?php _e ('There are no questions!', 'faqtastic'); ?></p>
<?php endif; ?>