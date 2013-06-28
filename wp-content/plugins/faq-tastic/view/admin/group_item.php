<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><td><a title="edit group" href="#" onclick="edit_group(<?php echo $group->id ?>); return false"><?php echo htmlspecialchars ($group->name); ?></a></td>
<td>
	<?php if ($group->page_id > 0) : ?>
		<a href="<?php bloginfo ('wpurl') ?>/wp-admin/page.php?action=edit&amp;post=<?php echo $group->page_id ?>"><?php echo $group->post_title ?></a>
	<?php endif; ?>
</td>
<td class="center">
	<a title="edit questions" href="<?php echo $this->base (); ?>?page=faq-tastic.php&amp;sub=questions&amp;questions=<?php echo $group->id ?>">
		<?php printf (__ngettext ('%d question', '%d questions', $group->questions, 'faqtastic'), $group->questions); ?>
	</a>
</td>
<td class="center">
	<?php $pending = Question::get_pending_count ($group->id); if ($pending > 0) : ?>
		<a title="edit pending" href="<?php echo $this->base (); ?>?page=faq-tastic.php&amp;sub=questions&amp;pending=<?php echo $group->id ?>"><?php echo $pending.' pending';  ?></a>
	<?php else : ?>
		-
	<?php endif; ?>
</td>
<td class="center" width="16">
<a href="#" onclick="delete_group(<?php echo $group->id ?>); return false" title="<?php _e ('Delete Group?', 'faqtastic'); ?>"><img src="<?php echo $this->url () ?>/images/delete.png" width="16" height="16" alt="<?php _e ('Delete', 'faqtastic'); ?>"/></a>
</td>
