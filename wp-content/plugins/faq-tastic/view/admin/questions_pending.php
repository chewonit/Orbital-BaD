<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div class="wrap">
	<h2><?php printf (__ ("Pending Questions for '%s'", 'faqtastic'), htmlspecialchars ($group->name)); ?></h2>
	<?php $this->submenu (true); ?>
	
	<div style="clear: both"></div>
	
	<?php if (count ($questions) > 0) : ?>
		<?php if ($pager->total > 25) :?>
		<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
		<?php endif; ?>
		
		<ul class="questions" id="questions">
			<?php foreach ($questions[$group->id] AS $question) : ?>
			<li id="question_<?php echo $question->id ?>"<?php if ($question->status == 'pending') echo ' class="pending"' ?>>
				<?php $this->render_admin ('question_item', array ('question' => $question)); ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($pager->total_pages () > 1) : ?>
		<div class="pagertools">
		<?php foreach ($pager->area_pages () AS $page) : ?>
			<?php echo $page ?>
		<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
		<div style="clear: both"></div>
		<a href="#" onclick="return select_all ()" title="<?php _e ('Select all', 'faqtastic'); ?>"><?php _e ('select all', 'faqtastic'); ?></a> | <a href="#" onclick="return delete_selected ()" title="<?php _e ('Delete selected', 'faqtastic'); ?>"><?php _e ('delete selected', 'faqtastic'); ?></a>
		
		<div id="loading" style="display: none">
			<img src="<?php echo $this->url () ?>/images/loading.gif" alt="<?php _e ('Loading', 'faqtastic'); ?>" width="32" height="32"/>
		</div>
		
	<?php else : ?>
	<p><?php _e ('There are no questions for this group', 'faqtastic'); ?></p>
	<?php endif; ?>
</div>
