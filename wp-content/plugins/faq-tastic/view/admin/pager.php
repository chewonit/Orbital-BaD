<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?><div id="pager" class="pager">
	<form method="get" action="<?php echo $pager->url ?>">

		<input type="hidden" name="page" value="faq-tastic.php"/>
		<?php if (isset ($_GET['questions'])) : ?>
		<input type="hidden" name="questions" value="<?php echo $_GET['questions'] ?>"/>
		<?php endif ;?>
		<input type="hidden" name="curpage" value="<?php echo $pager->current_page () ?>"/>
		<input type="hidden" name="sub" value="<?php echo $_GET['sub'] ?>"/>

		<?php _e ('Search', 'faqtastic'); ?>: 
		<input type="text" name="search" value="<?php echo htmlspecialchars ($_GET['search']) ?>"/>
		<?php _e ('Per page', 'faqtastic') ?>: 
		<select name="perpage">
			<?php foreach ($pager->steps AS $step) : ?>
		  	<option value="<?php echo $step ?>"<?php if ($pager->per_page == $step) echo ' selected="selected"' ?>><?php echo $step ?></option>
			<?php endforeach; ?>
		</select>
		
		<input class="button-primary" type="submit" name="Go" value="<?php _e ('Go', 'faqtastic') ?>"/>
	</form>
</div>
