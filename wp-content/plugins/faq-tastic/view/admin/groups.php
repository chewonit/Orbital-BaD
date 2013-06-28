<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?>
<div class="wrap">
	<h2><?php _e ('Question Groups', 'faqtastic'); ?></h2>
			<?php $this->submenu (true); ?>
			
	<?php if (count ($groups) > 0) : ?>
		<p style="clear: both">
		<?php _e ('Manage your Question Groups and add/edit questions &amp; answers using the links below.', 'faqtastic'); ?>
		</p>
		
		<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
		
		<table class="widefat post fixed list">
			<thead>
				<tr>
					<th><?php echo $pager->sortable ('name', __('Name', 'faqtastic')) ?></th>
					<th><?php echo $pager->sortable ('page_id', __('Attached page', 'faqtastic')) ?></th>
					<th class="center"><?php echo $pager->sortable ('questions', __('Questions', 'faqtastic')) ?></th>
					<th class="center"><?php _e('Pending','faqtastic'); ?></th>
					<th class="center" width="16"></th>
				</tr>
			</thead>

			<?php if ($pager->total_pages () > 1) : ?>
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="options">
					<?php foreach ($pager->area_pages () AS $page) : ?>
						<?php echo $page ?>
					<?php endforeach; ?>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php endif; ?>
			
			<tbody>
			<?php foreach ($groups AS $pos => $group) : ?>
			<tr id="group_<?php echo $group->id ?>"<?php if ($pos % 2 == 1) echo ' class="alt"' ?>>
				<?php $this->render_admin ('group_item', array ('group' => $group)); ?>
			</tr>
			<?php endforeach; ?>
			</tbody>

		</table>
		
		<p style="clear: both" class="notes">
		<b class="title"><?php _e ('Notes:', 'faqtastic'); ?></b><br />
		<?php _e ('<b>Change Question Group settings:</b> click on the link in the "Name" column for the group you want to amend.', 'faqtastic'); ?><br />
		<?php _e ('<b>Enable/Disable Ratings:</b> click on the link in the "Name" column for the group you want to change.', 'faqtastic'); ?><br />
		<?php _e ('<b>Add/Edit Questions:</b> click on the link in the "Questions" column of the group you want to add questions to.', 'faqtastic'); ?><br />
		<?php _e ('<b>Sort By Columns:</b> click on the link titles for the column and filter groups by using the search on the top right.', 'faqtastic'); ?><br />		
		<?php _e ('<b>Delete A Group:</b> use the red icon on the right hand side.', 'faqtastic'); ?><br />
		<?php _e ('Only Paged Question Groups show links in the "Attached Page" column.', 'faqtastic'); ?>
		</p>
		
	<?php else : ?>
		<p><?php _e ('There are no groups!', 'faqtastic'); ?></p>
	<?php endif; ?>
	
	<div id="loading" style="display: none">
		<img src="<?php echo $this->url () ?>/images/loading.gif" alt="<?php _e ('Loading', 'faqtastic'); ?>" width="32" height="32"/>
	</div>
</div>

<div class="wrap">
	<h2><?php _e ('Create Question Group', 'faqtastic'); ?></h2>
	<p><?php _e ('Question groups allow you to add questions &amp; answers into topically relevant groups. These groups of questions can be displayed either on the same page (Simple Group) OR with an index page with questions linking to sub-pages with answers (Paged Group).', 'faqtastic'); ?></p>
	
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<?php _e ('Group name', 'faqtastic'); ?>: <input type="text" name="group_name" value="" id="name"/> <?php _e ('Attached page', 'faqtastic'); ?>:
		<select name="page_id">
			<option value="0"><?php _e ('No page', 'faqtastic'); ?></option>
			<?php parent_dropdown ($group->page_id); ?>
		</select>
		
		<input class="button-secondary" type="submit" name="addgroup" value="<?php _e ('Add group', 'faqtastic'); ?>" id="group"/>
	</form>
	
	<p style="clear: both" class="notes">
		<b class="title"><?php _e ('Notes:', 'faqtastic'); ?></b><br />
		<?php _e ('To create a Simple question group, add a Group Name and select "No page" from the Attached Page dropdown list.', 'faqtastic'); ?><br />
		<?php _e ('To create a Paged question group, you will need to: 1) create a Page normally first; 2) add a Group Name and select the page you created from the Attached Page dropdown list.', 'faqtastic'); ?><br />
	</p>
</div>

<?php if (!empty ($groups)) : ?>
<div class="wrap">
	<h2><?php _e ('Publish an FAQ Page', 'faqtastic'); ?></h2>
	<p><?php _e ('Automatically create a page with the correct shortcode to display a <b>Simple</b> FAQ group.', 'faqtastic'); ?></p>
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">
		<table width="100%">
			<tr>
				<th width="120" align="right" valign="middle"><?php _e ('Page name', 'faqtastic'); ?>:</th>
				<td colspan="2"><input type="text" size="40" name="page_name" value=""/></td>
			</tr>
			<tr>
				<th align="right" valign="middle"><?php _e ('Question Group', 'faqtastic'); ?>:</th>
				<td colspan="2">
					<select name="group">
						<?php foreach ($groups AS $group) : ?>
						<option value="<?php echo $group->id ?>"><?php echo htmlspecialchars ($group->name) ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php if (FAQ_Features::is_pro ()) : ?>
				<?php $this->features->render_admin ('group_page_publish', array ('post' => $this->url ($_SERVER['REQUEST_URI']))); ?>
			<?php endif; ?>
			<tr>
				<th></th>
				<td><input class="button-secondary" type="submit" name="publish" value="<?php _e( 'Publish', 'faqtastic'); ?>"/></td>
			</tr>
		</table>
	</form>
	
	<p style="clear: both" class="notes">
		<b class="title"><?php _e ('Notes:', 'faqtastic'); ?></b><br />
		<?php _e ('Add a Page Name and select the Simple FAQ group from the Question Group dropdown list.', 'faqtastic'); ?><br />
	</p>
</div>
<?php endif; ?>