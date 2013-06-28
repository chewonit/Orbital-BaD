<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?>
<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">

<div class="wrap">
	<h2><?php _e ('FAQ-Tastic Options', 'faqtastic'); ?></h2>
		<?php $this->submenu (true); ?>
	<h2><?php _e ('Messages', 'faqtastic'); ?></h2>
		<table width="100%" style="clear: both">
			<tr>
				<th width="150" valign="top" align="right"><?php _e ('Thank-you message', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Displayed when a question is correctly submitted', 'faqtastic'); ?></span></th>
				<td>
					<textarea name="faq_thanks" rows="5" style="width: 95%"><?php echo htmlspecialchars ($thankyou) ?></textarea>
				</td>
			</tr>
			<tr>
				<th width="150" valign="top" align="right"><?php _e ('Failed message', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Displayed when a question is incorrectly submitted', 'faqtastic'); ?></span></th>
				<td>
					<textarea name="faq_failed" rows="5" style="width: 95%"><?php echo htmlspecialchars ($failed) ?></textarea>
				</td>
			</tr>
			
			<tr>
				<th width="150" valign="top" align="right"><?php _e ('Rating message', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Displayed when a question is rated', 'faqtastic'); ?></span></th>
				<td valign="top">
					<input type="text" style="width: 95%" name="faq_rating" value="<?php echo htmlspecialchars ($rating) ?>"/>
				</td>
			</tr>
			
			<tr>
				<th></th>
				<td><input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'faqtastic'); ?>"/></td>
			</tr>
		</table>
</div>

<div class="wrap">
	<h2><?php _e ('Email Options', 'faqtastic'); ?></h2>

	<table width="100%">
		<tr>
			<th width="150" valign="top" align="right"><?php _e ('Approved simple question', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Sent to the user when a Simple Group question is answered', 'faqtastic'); ?></span></th>
			<td>
				<textarea name="faq_approved_simple" rows="6" style="width: 95%"><?php echo htmlspecialchars ($approved_simple); ?></textarea><br/>
			</td>
		</tr>
		
		<tr>
			<th valign="top" align="right"><?php _e ('Approved paged question', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Sent to the user when a Paged Group question is answered', 'faqtastic'); ?></span></th>
			<td>
				<textarea name="faq_approved_paged" rows="6" style="width: 95%"><?php echo htmlspecialchars ($approved_paged); ?></textarea><br/>

			</td>
		</tr>
		
		<tr>
			<th valign="top" align="right"><?php _e ('Rejected question', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Sent to the user when a question is rejected', 'faqtastic'); ?></span></th>
			<td>
				<textarea name="faq_rejected" rows="6" style="width: 95%"><?php echo htmlspecialchars ($rejected); ?></textarea><br/>

			</td>
		</tr>

		<tr>
			<th valign="top" align="right"><?php _e ('Send email from', 'faqtastic'); ?>:<br/><span class="sub"><?php _e ('Address email is sent from', 'faqtastic'); ?></span></th>
			<td>
				<input type="text" name="admin_email_name" value="<?php echo htmlspecialchars ($options['admin_email_name']) ?>"/> (name)
				<input type="text" name="admin_email" value="<?php echo htmlspecialchars ($options['admin_email']) ?>"/> (email)
			</td>
		</tr>
		
		<tr>
			<th></th>
			<td><input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'faqtastic'); ?>" id="submit"/></td>
		</tr>
		</table>
</div>

<div class="wrap">
	<h2><?php _e ('General Options', 'faqtastic'); ?></h2>
	
	<table width="100%">
		<tr>
			<th width="170" align="right"><?php _e ('Admin role', 'faqtastic'); ?>:</th>
			<td>
				<select name="access_level">
					<?php global $wp_roles; foreach ($wp_roles->role_names as $key => $rolename) : ?>
						<option value="<?php echo $key ?>"<?php if ($options['access_level'] == $key) echo ' selected="selected"'; ?>><?php echo $rolename ?></option>
					<?php endforeach; ?>
				</select>
				
				<span class="sub"><?php _e ('Set the required user role level to use FAQ-Tastic', 'faqtastic'); ?></span>
			</td>
		</tr>
		<?php if (FAQ_Features::is_pro ()) : ?>
		<tr>
			<th align="right"><?php _e ('Author name &amp; website', 'faqtastic'); ?>:</th>
			<td>
				<input type="checkbox" name="show_author_details"<?php if ($options['show_author_details']) echo ' checked="checked"' ?>/>
				<span class="sub"><?php _e ('Ask for name and website details', 'faqtastic'); ?></span>
			</td>
		</tr>
		<tr>
			<th align="right" valign="top"><?php _e ('AdSense', 'faqtastic'); ?>:</th>
			<td>
				<textarea name="adsense" style="width: 95%" rows="4"><?php echo htmlspecialchars ($options['adsense']); ?></textarea>
			</td>
		</tr>
		<tr>
			<th align="right" valign="top"><?php _e ('AdSense Position', 'faqtastic'); ?>:</th>
			<td>
				<select name="adsense_position">
					<?php $this->select (array ('above' => 'Above', 'below' => 'Below'), $options['adsense_position']); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th align="right"><?php _e ('Textile', 'faqtastic'); ?>:</th>
			<td><input type="checkbox" name="textile"<?php echo $this->checked ($options['textile']) ?>/></td>
		</tr>
		<?php else : ?>
			<tr>
				<th align="right"><?php _e ('Ignore sad kittens', 'faqtastic'); ?>:</th>
				<td>
					<input type="checkbox" name="dontcare"<?php echo $this->checked ($options['dontcare']) ?>/>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th align="right"><?php _e ('Question order', 'faqtastic'); ?>:</th>
			<td>
				<select name="question_order">
					<?php echo $this->select (array ('default' => 'By position', 'rating' => 'By rating'), $options['question_order']); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th></th>
			<td><input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'faqtastic'); ?>" id="submit"/></td>
		</tr>
		
	</table>
</div>

</form>

<div class="wrap">
	<h2><?php _e ('Remove FAQ-Tastic', 'faqtastic'); ?></h2>
	<p><?php _e ('This option will completely remove FAQ-Tastic. All database tables, data and settings are completely removed.', 'faqtastic'); ?></p>
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<input class="button-primary" type="submit" name="remove" value="<?php _e ('Remove', 'faqtastic'); ?>"/>
	</form>
</div>

<div class="wrap">
	<h2><?php _e ('Special Tags', 'faqtastic'); ?></h2>
	<p><?php _e ('You can insert special tags into the thank-you, failed, and email messages.  These tags will be replaced with data from the question', 'faqtastic'); ?>:</p>
		<ul>
			<li><code>$question$</code> - <?php _e ('The question', 'faqtastic'); ?></li>
			<li><code>$answer$</code> - <?php _e ('The answer', 'faqtastic'); ?></li>
			<li><code>$message$</code> - <?php _e ('Approval or rejection message', 'faqtastic'); ?></li>
			<li><code>$page$</code> - <?php _e ('The URL of the answer (only for paged questions)', 'faqtastic'); ?></li>
	</ul>
</div>
