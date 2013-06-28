<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div style="float: right">
	<a href="#" onclick="delete_pending (<?php echo $id ?>); return false"><img src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/faq-tastic/images/delete.png" alt="<?php _e ('Delete', 'faqtastic'); ?>" width="16" height="16"/></a>
</div>

<?php if (strlen ($question['email']) > 0) : ?>
	<?php printf (__ ('<a href="#" onclick="edit_pending(%d); return false">%s</a> on <a href="%s">%s</a> <em>from %s</em>', 'faqtastic'), $id, htmlspecialchars ($question['question']), get_permalink ($question['group']), $page, $question['email']); ?>
<?php else: ?>
	<?php printf (__ ('<a href="#" onclick="edit_pending(%d); return false">%s</a> on <a href="%s">%s</a>', 'faqtastic'), $id, htmlspecialchars ($question['question']), get_permalink ($question['group']), $page); ?>
<?php endif; ?>
