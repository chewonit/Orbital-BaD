<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div class="wrap">
	<h2><?php _e ('Question Sources', 'faqtastic'); ?></h2>
	<?php $this->submenu (true); ?>
	<p><?php _e ('This is a list of people who asked questions:', 'faqtastic'); ?></p>
	
	<?php if (count ($sources) > 0) : ?>
		<ul class="questions">
		<?php foreach ($sources AS $source) :?>
			<li><?php echo $source->author_email ?></li>
		<?php endforeach; ?>
		</ul>
		
		<?php if ($pager->total_pages () > 1) : ?>
		<div class="pagertools">
		<?php foreach ($pager->area_pages () AS $page) : ?>
			<?php echo $page ?>
		<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
	<?php else : ?>
		<p><?php _e ('There are no question sources.', 'faqtastic'); ?></p>
	<?php endif; ?>
</div>