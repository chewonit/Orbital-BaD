<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div class="wrap">
	<h2><?php _e ('Pending Questions', 'faqtastic'); ?></h2>
	<?php $this->submenu (true); ?>
	<p style="clear: both">
	<?php _e ('Pending questions are user submitted questions that are waiting to be answered.', 'faqtastic'); ?><br>
	<?php _e ('Click on the links below to answer, approve or reject pending questions.', 'faqtastic'); ?>
	</p>
	
	<?php if (count ($questions) > 0) : ?>
		<?php if ($pager->total > 25) :?>
		<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
		<?php endif; ?>

		<ul>
		<?php foreach ($questions AS $group => $quests) : ?>
			<li>
				<h3><?php echo $quests[0]->name; ?></h3>
				<ul class="questions">
					<?php foreach ($quests AS $question) : ?>
					<li id="question_<?php echo $question->id ?>"<?php if ($question->status == 'pending') echo ' class="pending"' ?>>
						<?php $this->render_admin ('question_item', array ('question' => $question)); ?>
					</li>
					<?php endforeach; ?>
				</ul>
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
		
		<a href="#" class="button-secondary" onclick="return select_all ()" title="<?php _e ('Select all', 'faqtastic'); ?>"><?php _e ('select all', 'faqtastic'); ?></a> 
		<a href="#" class="button-secondary warning" onclick="return delete_selected ()" title="<?php _e ('Delete selected', 'faqtastic'); ?>"><?php _e ('delete selected', 'faqtastic'); ?></a>
		
		<div id="loading" style="display: none">
			<img src="<?php echo $this->url () ?>/images/loading.gif" alt="<?php _e ('Loading', 'faqtastic'); ?>" width="32" height="32"/>
		</div>
		
		<p style="clear: both" class="notes">
		<b class="title"><?php _e ('Notes:', 'faqtastic'); ?></b><br />
		<?php _e ('<b>Delete Pending Question:</b> use the red icon on the right hand side.', 'faqtastic'); ?><br>
		<?php _e ('Pending questions have a pink background. Approved questions will turn green.', 'faqtastic'); ?><br>
		<?php _e ('You can also manage and add new questions and Question Groups on the <a href="tools.php?page=faq-tastic.php&amp;sub=questions">Questions &amp; Groups</a> page.', 'faqtastic'); ?>
		</p>
		
	<?php else : ?>
	<p><?php _e ('There are no pending questions', 'faqtastic'); ?></p>
	<?php endif; ?>
</div>
