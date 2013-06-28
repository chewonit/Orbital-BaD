<?php if (!defined ('ABSPATH')) die ('Not allowed'); ?>

<?php echo "<p>A question needs your approval:</p>"; ?>

<?php echo "<p>\"".htmlspecialchars ($question->question)."\"</p>"; ?>

<?php 
    echo _e('Author email:', 'faqtastic');
    echo ((htmlspecialchars ($question->author_email)=="") ? _e("No email provided.") : "<a href=\"mailto:".htmlspecialchars ($question->author_email)."\">".htmlspecialchars ($question->author_email)."</a>"); ?>

<?php echo "<p>" ?>
<?php _e ('You can approve this question here:', 'faqtastic'); ?>
<?php echo "</p>"; ?>

<?php echo "<a href=\"".get_bloginfo('siteurl')."/wp-admin/tools.php?page=faq-tastic.php\">Click here to answer</a>."; ?>