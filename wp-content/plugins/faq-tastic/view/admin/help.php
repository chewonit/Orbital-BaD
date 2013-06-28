<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?>
<div class="wrap">
	<h2><?php _e ('FAQ-Tastic Help (Quick Reference)', 'faqtastic'); ?></h2>
		<?php $this->submenu (true); ?>
	<p style="clear: both"><?php _e ('FAQ-Tastic is a plugin to create and manage <b>multiple</b> <a href="http://en.wikipedia.org/wiki/FAQ" target="_blank">FAQ</a>\'s (Frequently Asked Questions) on your WordPress site.', 'faqtastic'); ?></p>
	<p><?php _e ('At <a href="http://faq-tastic.com/" target="_blank">FAQ-Tastic.com</a> we understand that not all of your questions fit nicely into one category! For example, you may need one FAQ for beginners and another one for advanced users of your site.', 'faqtastic'); ?></p>
	<p><?php _e ('If you need to create several different FAQ\'s for each section of your site, then FAQ-Tastic is the perfect plugin for you!', 'faqtastic'); ?></p>
	
	<h2><?php _e ('Question Groups', 'faqtastic'); ?></h2>
	<p>
		<?php _e ('Every question and answer must belong to a Question Group.', 'faqtastic'); ?>
		<?php printf (__ ('You can create a Question Group from the FAQ-Tastic <a href="%s?page=faq-tastic.php&amp;sub=questions">administration interface</a>.  FAQ-Tastic allows two types of Question Groups:', 'faqtastic'), $this->base ()); ?>
	</p>
	
	<ol>
		<li><strong><?php _e ('Simple', 'faqtastic'); ?></strong> - <?php _e ('Question &amp; answer is displayed on the same page.', 'faqtastic'); ?></li>
		<li><strong><?php _e ('Paged', 'faqtastic'); ?></strong> - <?php _e ('Questions appear on one page with links to the answers on separate WordPress Pages.<br /><small><b>Note:</b> Perfect for creating additional keyword targetted content for your site - purposely built for <a href="http://en.wikipedia.org/wiki/Search_engine_optimization" target="_blank">SEO</a> (Search Engine Optimization) experts.</small>', 'faqtastic'); ?></li>
	</ol>

	<p><?php _e ('When creating a Question Group you can decide which type you want by attaching it to a page or not - a Simple group has no attached group, a Paged group is attached to an existing page.', 'faqtastic'); ?></p>
	<p><?php _e ('You <b>must</b> give a Question Group a name.  This name is used to uniquely refer to the group, and is used when display the FAQ in your blog.', 'faqtastic'); ?></p>
	
	<h2><?php _e ('Displaying an FAQ on your site', 'faqtastic'); ?></h2>
	<p><?php _e ('FAQ-Tastic can display a list of questions and answers from any Question Group you\'ve created. To do this, simply insert the special WordPress shortcode in the text of any Page or Post:', 'faqtastic'); ?></p>
	
	<p><code>[faq list <strong>name</strong>]</code></p>
	
	<p><?php _e ('Where <code><strong>name</strong></code> is the name of a Question Group.', 'faqtastic'); ?></p>
	
	<h2><?php _e ('Displaying "Jump Links" (anchor links) on your site', 'faqtastic'); ?></h2>
	<p><?php _e ('FAQ-Tastic can display a list of question links on any Page or Post so users can jump down to each answer quickly. To do this simply insert the following special WordPress shortcode in the text of any Page or Post:', 'faqtastic'); ?></p>
	
	<p><code>[faq summary <strong>name</strong>]</code></p>
	
	<p><?php _e ('Where <code><strong>name</strong></code> is the name of a Question Group.', 'faqtastic'); ?></p>
	
	<h2><?php _e ('Adding questions to your site', 'faqtastic'); ?></h2>
	<p><?php _e ('Questions can be directly added from the FAQ-Tastic interface by clicking on the \'questions\' link of a Question Group.  This will take you to a page where you can view existing questions and add a new one.', 'faqtastic'); ?></p>
	
	<h2><?php _e ('Allowing visitors to ask their questions', 'faqtastic'); ?></h2>
	<p><?php _e ('FAQ-Tastic can display a form for your website visitors to ask their questions. To do this simply insert the following special WordPress shortcode in the text of any page or post:', 'faqtastic'); ?></p>
	
	<p><code>[faq ask <strong>name</strong>]</code></p>
	
	<p><?php _e ('Where <code><strong>name</strong></code> is the name of a Question Group.', 'faqtastic'); ?></p>
</div>