<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div style="float: right">
	<?php if ($question->positive != 0 || $question->negative != 0) : ?>
	<?php printf ("+%d/-%d", $question->positive, $question->negative); ?>
	<?php endif; ?>
	
	<?php if ($question->page_id > 0) : ?>
		<a href="<?php echo get_permalink ($question->page_id) ?>"><img src="<?php echo $this->url () ?>/images/view.png" width="16" height="16" alt="<?php _e ('View', 'faqtastic'); ?>"/></a>
	<?php endif;?>
	<a href="#" onclick="delete_question (<?php echo $question->id ?>); return false"><img src="<?php echo $this->url () ?>/images/delete.png" alt="<?php _e ('Delete', 'faqtastic'); ?>" width="16" height="16"/></a>
</div>

<input type="checkbox" name="select[]" value="<?php echo $question->id ?>"/>

<a href="#" onclick="edit_question (<?php echo $question->id ?>); return false"><?php echo htmlspecialchars (html_entity_decode ($question->question)); ?></a> <span class="sub">(#<?php echo $question->id ?>)</span>