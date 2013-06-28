<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><?php if (count ($related) > 0) : ?>
<div class="related">
	<?php _e ('Related', 'faqtastic'); ?>:
	<ul class="faq-related">
	<?php foreach ($related as $item) : ?>
		<li><?php echo $item->link (); ?></li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>