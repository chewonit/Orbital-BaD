<?php
/*
Template Name: Edit Module Page
*/
?>
<?php
global $current_user,$wpdb;
get_currentuserinfo();
$username = $current_user->user_login;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
ob_start();
function my_ofset($text){
    preg_match('/^\D*(?=\d)/', $text, $m);
    return strlen($m[0]);
}
if(isset($_GET['editid_txt'])) { 
	global $editid;
	$editid = $_GET['editid_txt'];
}else if(isset($_GET['updateid_txt'])) {
	$level = $_GET['modulecode_txt'][my_ofset($_GET['modulecode_txt'])];
	$updateid = $_GET['updateid_txt'];
	$modulecode = strtoupper($_GET['modulecode_txt']);
	$dupe = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.modulecode='{$modulecode}' AND $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.id !={$updateid}" );
	if($dupe == 0) {
		$wpdb->update( 
			'wp_moduledata',
			array(
				'modulecode' => $modulecode,
				'modulename' => ucwords(strtolower($_GET['modulename_txt'])),
				'modulepreq' => strtoupper($_GET['modulepreq_txt']),
				'level' => $level
			),
			array( 'id' => $updateid ),
			array( 
				'%s',
				'%s',
				'%s',
				'%d'
			), 
			array( '%d' ) 
		);
		wp_redirect( get_permalink() . "/?editid_txt=" . $_GET['updateid_txt'] ); exit;
		echo "editid_txt";
	} else {
		// Duplicate exist
	}
} else {
	// Redirect to Modules Page
	wp_redirect( get_permalink( get_page( 13 ) ) ); exit;
}
ob_end_clean();
?>
<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap clearfix">

						<div id="main" class="ninecol first clearfix" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<header class="article-header">

									<h1 class="page-title"><?php the_title(); ?></h1>
									<!--<p class="byline vcard"><?php
										printf(__('Posted <time class="updated" datetime="%1$s" pubdate>%2$s</time> by <span class="author">%3$s</span>.', 'bonestheme'), get_the_time('Y-m-j'), get_the_time(__('F jS, Y', 'bonestheme')), bones_get_the_author_posts_link());
									?></p>-->


								</header> <!-- end article header -->

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
								</section> <!-- end article section -->
								
								<?php
									//echo "Module to edit{$editid}";
									$valid = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.id='{$editid}'" );
									echo "<p>Total Module Count: {$valid}</p>";
									
									$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.id='{$editid}'" );
									$rawproducts = $wpdb->get_results( $query );
									
									global $modulecode, $modulename, $modulepreq;
									$modulecode = print_r($rawproducts->modulecode);
									$modulename = print_r($rawproducts->modulename);
									$modulepreq = print_r($rawproducts->modulepreq);
								?>
								<script>
								function fieldCheck() {
									if ($("#editmodulecode_txt").val().length != 0 && $("#editmodulename_txt").val().length != 0) {
										$("#button1").attr("disabled", false);
									} else {
										$("#button1").attr("disabled", true);
									}
								}
								$(document).ready(function(){
									fieldCheck();
									$("#editmodulecode_txt").keyup(function(){
										fieldCheck();
									});
									$("#editmodulename_txt").keyup(function(){
										fieldCheck();
									});
									$("#editmodulepreq2_txt").keyup(function(){
										fieldCheck();
										if ($(this).val().length != 0) {
											$("#addPreq").attr("disabled", false);
										} else {
											$("#addPreq").attr("disabled", true);
										}
									});
									
									function loadPreqList() {
										var preqString = $("#editmodulepreq_txt").val();
										if(preqString == "") return;
										var arr = preqString.split("+");
										for(i=0; i<arr.length; i++) {
											$("#preq-list").append( "<div class='preq-tag' style='float:left;'>" + arr[i] + "</div>" );
										}
									}
									loadPreqList();
									function popluatePreqList() {
										var elems = $(".preq-tag").nextAll(), countPreq = elems.length;
										var preqString = "";
										if($.trim($(".preq-tag").text()).length == 0){
											$("#editmodulepreq_txt").val("");
										}
										$(".preq-tag").each(function() {
											if( countPreq != 0) {
												preqString += $(this).html() + "+";
											} else {
												preqString += $(this).html();
												$("#editmodulepreq_txt").val(preqString);
											}
											countPreq --;
										});
									}
									$("#addPreq").click(function(){
										$("#preq-list").append( "<div class='preq-tag' style='float:left;'>" + $("#editmodulepreq2_txt").val() + "</div>" );
										$("#editmodulepreq2_txt").val("");
										$("#addPreq").attr("disabled", true);
									
										popluatePreqList();
									});
									$(document).on("click", ".preq-tag", function() {
										$(this).remove();
										popluatePreqList();
									});
								});
								</script>
								
								<?php
									foreach($rawproducts as $a) {
										
										echo '<form name="input" action="'. get_permalink() .'" method="get">'
											.'<div>'
											.'<div class="input-module"><div>Module code: </div><div><input id="editmodulecode_txt" type="text" name="editmodulecode_txt" value="' . $a->modulecode . '"></div></div>'
											.'<div class="input-module"><div>Module name: </div><div><input id="editmodulename_txt" type="text" name="editmodulename_txt" value="' . $a->modulename . '"></div></div>'
											.'</div>'
											.'<br style="clear:both;" />'
											.'<div>'
											.'<div class="input-module"><div>Module Prerequisite: </div>'
											.'<div>'
											.'<div style="float:left;"><input id="editmodulepreq2_txt" type="text" name="editmodulepreq2_txt"></div>'
											.'<input id="editmodulepreq_txt" type="text" name="editmodulepreq_txt" value="' . $a->modulepreq . '" style="display:none">'
											.'<div style="float:left; margin: 0 3px;"><input id="addPreq" type="button" value="Add Prerequisite" disabled=true></div>'
											.'<div id="preq-list" class="preq-list" style="float:left; margin: 0 3px;"></div>'
											.'</div></div>'
											.'<input id="updateid_txt" type="text" name="updateid_txt" style="display:none" value="' . $a->id . '">'
											.'</div>'
											.'<br /><div class="input-module-submit"><input id="button1" type="submit" value="Submit" disabled=true></div></form>';
										if($a->modulepreq==null) {
											$preq = "nil";
										} else {
										    $preq = $a->modulepreq;
										}
										echo '<div class="module-item">';
										print_r('<div style="float:left">' . $a->modulecode . ": " . $a->modulename . "</div>");
										echo "<br style='clear:both;'/>";
										echo "<div style='float:left; margin-right:10px;'>Level: ". $a->level ."000</div><div>Prerequisite: {$preq}</div><div style='clear:both;'></div></div>";
										
									}
								?>
								
								
								<footer class="article-footer">
									<p class="clearfix"><?php the_tags('<span class="tags">' . __('Tags:', 'bonestheme') . '</span> ', ', ', ''); ?></p>

								</footer> <!-- end article footer -->

								<?php comments_template(); ?>

							</article> <!-- end article -->

							<?php endwhile; else : ?>

									<article id="post-not-found" class="hentry clearfix">
											<header class="article-header">
												<h1><?php _e("Oops, Post Not Found!", "bonestheme"); ?></h1>
										</header>
											<section class="entry-content">
												<p><?php _e("Uh Oh. Something is missing. Try double checking things.", "bonestheme"); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e("This is the error message in the page-custom.php template.", "bonestheme"); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</div> <!-- end #main -->

						<?php get_sidebar(); ?>

				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->

<?php get_footer(); ?>
