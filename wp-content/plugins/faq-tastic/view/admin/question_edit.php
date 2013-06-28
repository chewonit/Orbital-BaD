<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?>
<strong><?php _e ('Editing Question', 'faqtastic'); ?></strong>

<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8" id="edit_<?php echo $question->id ?>" onsubmit="save_question(<?php echo $question->id ?>,this);return false">
	<table width="100%">
		<tr>
			<th width="110" valign="top"><?php _e ('Question', 'faqtastic'); ?>:</th>
			<td><textarea style="width: 95%" name="faq_question" rows="4"><?php echo htmlspecialchars ($question->question); ?></textarea></td>
		</tr>
		<tr>
			<th valign="top"><?php _e ('Answer', 'faqtastic'); ?>:</th>
			<td><textarea style="width: 95%" name="faq_answer" rows="4"><?php echo htmlspecialchars ($question->answer); ?></textarea></td>
		</tr>

<?php if ($group->page_id > 0) : ?>
		<tr>
			<th></th>
			<td>
		<input type="hidden" name="page_title" value="<?php echo htmlspecialchars ($page ? $page->post_title : $question->question); ?>" style="width: 95%"/>
			</td>
		</tr>
		<tr>
			<th></th>
			<td>
		<input type="hidden" name="page_url" value="<?php echo htmlspecialchars ($page ? $page->post_name : sanitize_title ($question->question)) ?>" style="width: 95%"/>
			</td>
		</tr>

<?php endif; ?>
		
<?php if ($question->status == 'pending') : ?>
		<tr>
			<th valign="top"><?php _e ('Message', 'faqtastic'); ?>:<br/><span class="sub"><?php _e('Text added to approval or rejection email', 'faqtastic'); ?></span></th>
			<td><textarea style="width: 95%" name="faq_message" rows="4"></textarea></td>
		</tr>
<?php endif; ?>
		
		<tr>
			<th><?php _e ('Author email', 'faqtastic'); ?>:</th>
			<td><input style="width: 45%" type="text" name="author_email" value="<?php echo htmlspecialchars ($question->author_email) ?>"/></td>
		</tr>
		
<?php if ($question->status != 'pending') : ?>
		<tr>
			<th><?php _e ('Positive rating', 'faqtastic'); ?>:</th>
			<td>
				<input type="text" name="faq_positive" value="<?php echo $question->positive ?>" size="5"/>
			</td>
		</tr>
		<tr>
			<th><?php _e ('Negative rating', 'faqtastic'); ?>:</th>
			<td>
		<input type="text" name="faq_negative" value="<?php echo $question->negative ?>" size="5"/>
			</td>
		</tr>
<?php endif; ?>

		<tr>
			<th></th>
			<td>
<?php if ($question->status == 'pending') : ?>
				<input class="button-primary" type="submit" name="save" value="<?php _e ('Approve', 'faqtastic'); ?>"/>
				<input class="button-primary" type="submit" name="reject" value="<?php _e ('Reject', 'faqtastic'); ?>" onclick="reject_question(<?php echo $question->id ?>,$('edit_<?php echo $question->id ?>')); return false"/>
				<input class="button-primary" type="submit" name="cancel" value="<?php _e ('Decide later', 'faqtastic'); ?>" onclick="cancel_question(<?php echo $question->id ?>); return false"/>
<?php else : ?>
				<input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'faqtastic'); ?>"/>
				<input class="button-primary" type="submit" name="cancel" value="<?php _e ('Cancel', 'faqtastic'); ?>" onclick="cancel_question(<?php echo $question->id ?>); return false"/>
<?php endif; ?>

<?php if ($question->status == 'pending') : ?>	
				 <span class="sub"><?php _e ('An answer is needed before a question is approved', 'faqtastic'); ?></span>
<?php endif; ?>
			</td>
		</tr>
	</table>
</form>