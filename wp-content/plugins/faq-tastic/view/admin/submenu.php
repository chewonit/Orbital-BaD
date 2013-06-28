<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?>
<ul <?php echo $class ?>>
  <li><a <?php if ($sub == '') echo 'class="current"'; ?>href="<?php echo $url ?>"><?php _e ('Pending', 'faqtastic') ?></a><?php echo $trail; ?></li>
  <li><a <?php if ($sub == 'questions') echo 'class="current"'; ?>href="<?php echo $url ?>&amp;sub=questions"><?php _e ('Questions &amp; Groups', 'faqtastic') ?></a><?php echo $trail; ?></li>
  <li><a <?php if ($sub == 'options') echo 'class="current"'; ?>href="<?php echo $url ?>&amp;sub=options"><?php _e ('Options', 'faqtastic') ?></a><?php echo $trail; ?></li>
  <li><a <?php if ($sub == 'help') echo 'class="current"'; ?>href="<?php echo $url ?>&amp;sub=help"><?php _e ('Help', 'faqtastic') ?></a><?php echo $trail; ?></li>
  <li><a <?php if ($sub == 'resources') echo 'class="current"'; ?>href="<?php echo $url ?>&amp;sub=resources"><?php _e ('Resources', 'faqtastic') ?></a></li>
</ul>