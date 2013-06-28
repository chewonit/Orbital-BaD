<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div class="wrap">
	
	<h2><?php printf (__ ("Questions for '%s'", 'faqtastic'), htmlspecialchars ($group->name)); ?></h2>
	<?php $this->submenu (true); ?>
		
		<?php if (FAQ_Features::is_pro ()) : ?>
		<div class="pagertools">
			<a href="<?php echo $this->url () ?>/pro/csv.php?id=<?php echo $group->id ?>&amp;type=group" title="<?php _e ('Download CSV File', 'faqtastic'); ?>"><img src="<?php echo $this->url () ?>/images/csv.png" width="50" height="50" alt="<?php _e ('Download CSV File', 'faqtastic'); ?>"/></a>
		</div>
		<?php endif; ?>
		
	<p style="clear: none; float:left;">
		<?php _e ('Edit existing question by using the links below.', 'faqtastic'); ?><br />
		<?php _e ('To add a new question, scroll down and use the <a href="#add">Add Question</a> section.', 'faqtastic'); ?><br />
		<?php if (FAQ_Features::is_pro ()) : ?>
			<?php _e ('You can export and download all questions (and answers) for this Question Group as a CSV file.', 'faqtastic'); ?><br />
			<?php _e ('To do this, click on the "Download CSV File" icon on the right of this page.', 'faqtastic'); ?><br />
			<?php _e ('To import a list of questions, scroll down and use the <a href="#add">Import CSV File</a> section below.', 'faqtastic'); ?><br />
		<?php endif; ?>
	</p>
	<div style="clear:both;"></div>
	<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
	<?php if (count ($questions) > 0) : ?>
		<ul class="questions" id="questions">
			<?php foreach ($questions AS $question) : ?>
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
		
		<?php //if (count ($questions) > 1 && $this->is_pro()) : ?>
		<?php if (count ($questions) > 1 ) : ?>
		<script type="text/javascript" charset="utf-8">
			Sortable.create ('questions', { ghosting: false, onUpdate: function (item) { save_order (<?php echo $group->id ?>,<?php echo $pager->offset (); ?>); } });
		</script>
		<?php endif; ?>
		
		<a href="#" class="button-secondary" onclick="return select_all ()"><?php _e('select all','faqtastic'); ?></a> 
		<a href="#" class="button-secondary warning" onclick="return delete_selected ()"><?php _e('delete selected','faqtastic'); ?></a>
		
		<div style="clear: both"></div>
		
		<div id="loading" style="display: none">
			<img src="<?php echo $this->url () ?>/images/loading.gif" alt="<?php _e ('Loading', 'faqtastic'); ?>" width="32" height="32"/>
		</div>
		
		<p style="clear: both" class="notes">
			<b class="title"><?php _e ('Notes:', 'faqtastic'); ?></b><br />
			<?php _e ('<b>Re-Ordering Questions:</b> drag and drop the questions. Changes will appear immediately on your site.', 'faqtastic'); ?><br />
		</p>
		
	<?php else : ?>
	<p><?php _e ('There are no questions', 'faqtastic'); ?></p>
	<?php endif; ?>
</div>

<div class="wrap">
	<h2><a name="add" id="add"></a><?php _e ('Add Question', 'faqtastic'); ?></h2>
	<p><?php _e ('To add a new question, just type your question and answer in the fields below.', 'faqtastic'); ?></p>
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<table width="100%">
			<tr>
				<th width="100" valign="top"><?php _e ('Question', 'faqtastic'); ?>:</th>
				<td><textarea style="width: 95%" name="faq_question" rows="4"></textarea></td>
			</tr>
			<tr>
				<th valign="top"><?php _e ('Answer', 'faqtastic'); ?>:</th>
				<td><textarea style="width: 95%" name="faq_answer" rows="4"></textarea></td>
			</tr>
			<tr>
				<th></th>
				<td><input class="button-primary" type="submit" name="addquestion" value="<?php _e ('Add New Question', 'faqtastic'); ?>" id="question"/></td>
			</tr>
		</table>
	</form>
	<p style="clear: both" class="notes">
		<b class="title"><?php _e ('Notes:', 'faqtastic'); ?></b><br />
		<?php _e ('Adding a question will automatically approve it. The question will appear immediately on your website.', 'faqtastic'); ?><br />
	</p>
</div>

<?php if (FAQ_Features::is_pro ()) : ?>
	<?php $this->features->render_admin ('import', array ('post' => $this->url ($_SERVER['REQUEST_URI']))); ?>
<?php endif; ?>