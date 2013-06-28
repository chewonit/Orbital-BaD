<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><form action="" method="post" onsubmit="return save_group(<?php echo $group->id ?>,this);">
<table width="100%">
	<tr>
		<th width="100"><?php _e ('Name', 'faqtastic'); ?>:</th>
		<td><input style="width: 95%" type="text" name="group_name" value="<?php echo htmlspecialchars ($group->name) ?>"/></td>
	</tr>
	<tr>
		<th width="100"><label for="ratings_<?php echo $group->id ?>"><?php _e ('Allow ratings', 'faqtastic'); ?>:</label></th>
		<td><input id="ratings_<?php echo $group->id ?>" type="checkbox" name="group_ratings"<?php if ($group->ratings) echo ' checked="checked"'?>/></td>
	</tr>
	<tr>
		<th></th>
		<td>
			<input class="button-secondary" type="submit" name="submit" value="<?php _e ('Save', 'faqtastic'); ?>"/>
			<input class="button-secondary" type="submit" name="cancel" value="<?php _e ('Cancel', 'faqtastic'); ?>" onclick="Modalbox.hide (); return false"/>
		</td>
	</tr>
</table>
</form>