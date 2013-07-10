<?php
/*
Template Name: Plan
*/
?>
<?php
global $current_user,$wpdb,$username;
get_currentuserinfo();
$username = $current_user->user_login;
if (!isset($wpdb->moduledata)) {
	$wpdb->moduledata = $table_prefix . 'moduledata';
}
?>
<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap clearfix">

						<div id="main" class="ninecol first clearfix" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<header class="article-header">

									<!--<h1 class="page-title"><?php //the_title(); ?></h1>-->
									<!--<p class="byline vcard"><?php
										printf(__('Posted <time class="updated" datetime="%1$s" pubdate>%2$s</time> by <span class="author">%3$s</span>.', 'bonestheme'), get_the_time('Y-m-j'), get_the_time(__('F jS, Y', 'bonestheme')), bones_get_the_author_posts_link());
									?></p>-->


								</header> <!-- end article header -->

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
								</section> <!-- end article section -->
								
								<script>
								var moduleChnageList = new Array();
								var tempListHolder = "";
								$(function() {
									$( "ul.module-plan-list" ).sortable({
										connectWith: ".module-plan-list",
										start: function(event, ui) {
											tempListHolder = ui.item.parent().get(0).id;
										},
										stop: function(event, ui) {
											if(tempListHolder != ui.item.parent().get(0).id) {
												
												if( ui.item.attr('year') == ui.item.parent().attr('year') && ui.item.attr('sem') == ui.item.parent().attr('sem')) {
													delete moduleChnageList[ui.item.attr("id")];
												} else {
													moduleChnageList[ui.item.attr("id")] = ui.item.parent().attr('year') + "." + ui.item.parent().attr('sem');
												}
												moduleChangesHtml = "";
												for(mod in moduleChnageList) {
												  moduleChangesHtml += "<div>" + mod
													+ " -> Year "
													+ moduleChnageList[mod].charAt(0)
													+ " Sem " + moduleChnageList[mod].charAt(2)
													+"</div>";
												}
												if(moduleChangesHtml == "") {
													$('#module-list-changes').html("No changes.");
													$('.update-module-btn').prop("disabled", true);
													$('.update-module-btn').slideUp();
													$('#change-widget').slideUp();
												} else {
													$('#module-list-changes').html(moduleChangesHtml);
													$('.update-module-btn').prop("disabled", false);
													$('.update-module-btn').slideDown();
													$('#change-widget').slideDown();
												}
											}
										}
									});
									$( ".module-plan-list" ).disableSelection();
									$( "#dialog-message" ).dialog({
											autoOpen: false,
											modal: true,
											buttons: {
											Ok: function() {
												$( this ).dialog( "close" );
											},
											show: {
												effect: "blind",
												duration: 1000
											},
											hide: {
												effect: "explode",
												duration: 1000
											}
										}
									});
								});
								
								/*
									Function to update the modules
								*/
								function updateModules(str) {
									
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											switch(xmlhttp.responseText) {
												case '1':
													$('#dialog-message').dialog('option', 'title', 'Update Complete');
													$('#dialog-content').html("Modules Updated");
													break;
												/*case '2':
													$('#dialog-message').dialog('option', 'title', 'Error');
													$('#dialog-content').html("Some modules could not be updated");
													break
												case '3':
													$('#dialog-message').dialog('option', 'title', 'Error');
													$('#dialog-content').html("The modules could not be updated");
													break;*/
											}
											$( "#dialog-message" ).dialog( "open" );
																						
											$("#loading-overlay").fadeOut(200, function(){
												$("#loading-overlay").removeClass("loading-overlay");
											});
											$("#loading-overlay-message").fadeOut(200, function(){
												$("#loading-overlay-message").removeClass("loading-overlay-message");
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-plan-functions/?"+str,true);
									xmlhttp.send();
									
									$("#loading-overlay").fadeIn();
									$("#loading-overlay").addClass("loading-overlay");
									$("#loading-overlay-message").fadeIn();
									$("#loading-overlay-message").addClass("loading-overlay-message");
									
									$('#module-list-changes').slideUp(200, function(){
										$('#module-list-changes').html("No changes.");
										$('#module-list-changes').slideDown();
									});
									
									$('.update-module-btn').prop("disabled", true);
									$('.update-module-btn').slideUp();
									
									$('#change-widget').slideUp();
								}
								
								/*
									Function to filter out semesters
								*/
								function filterSemester(element) {
									$('#list-'+element.name.charAt(0)+'-'+element.name.charAt(1)+'-container').slideToggle();
								}
								function clearFilters() {
									$('.list-container').each(function(){
										$(this).slideDown();
									});
									$('.filterCheckbox').each(function(){
										$(this).attr('checked',false);
									});
								}
								
								$(document).ready(function(){
									$(".update-module-btn").click(function(){
										str = "ur=<?php echo $username ?>&mc=";
										yrStr = "";
										semStr = "";
										for(mod in moduleChnageList) {
											str += mod + ",";
											yrStr += moduleChnageList[mod].charAt(0) + ",";
											semStr += moduleChnageList[mod].charAt(2) + ",";
											$('#'+mod).attr('year',moduleChnageList[mod].charAt(0));
											$('#'+mod).attr('sem',moduleChnageList[mod].charAt(2));
										}
										updateModules(str.slice(0,-1)
											+ "&yr=" + yrStr.slice(0,-1) 
											+ "&sem=" + semStr.slice(0,-1));
										
									});
								});
								</script>
								
								<?php
									$moduleArray = array
									(
										array("empty",	// All unideicated modules
											array()
										),
										array("empty",	// Year 1
											array(),	// Sem 1
											array()		// Sem 2
										),
										array("empty",	// Year 2
											array(),	// Sem 1
											array()		// Sem 2
										),
										array("empty",	// Year 3
											array(),	// Sem 1
											array()		// Sem 2
										),
										array("empty",	// Year 4
											array(),	// Sem 1
											array()		// Sem 2
										),
									);
								
									$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' ORDER BY $wpdb->moduledata.year,$wpdb->moduledata.sem,$wpdb->moduledata.modulecode" );
									$rawmodule = $wpdb->get_results( $query );
									
									foreach($rawmodule as $a) {
										$tempArray = &$moduleArray[$a->year][$a->sem];
										$style = "";
										
										if($a->istaken) {
											$style = " module-istaken";
										} else if($a->status=="available"){
											$style = " module-available";
										} else {
											$style = "";
										}
										
										$modInfoArr = array(
											'code' => $a->modulecode,
											'name' => $a->modulename,
											'style' => $style
										);
										array_push($tempArray, $modInfoArr);
									}
									
									echo "<p>";
									for($i=1; $i<=4; $i++) {
										for($j=1; $j<=2; $j++) {
											echo "<div id='list-{$i}-{$j}-container' class='list-container'><h5 style='margin-bottom:0;'>Year {$i} Semester {$j}</h5>"
												."<ul id='list-{$i}-{$j}' year='{$i}' sem='{$j}' class='module-plan-list'>";
											
											foreach($moduleArray[$i][$j] as $mod) {
												echo "<li id='{$mod['code']}' year='{$i}' sem='{$j}' class='module-item{$mod['style']}'>{$mod['code']}: {$mod['name']}</li>";
											}
											echo "</ul>"
												."<input id='update-module-btn' type='button' value='Update Modules' class='update-module-btn' style='margin: 5px 0 0 3px; display:none;'/>"
												."</div>";
										}
									}
									echo "<div id='list-0-1-container' class='list-container'><h5 style='margin-bottom:0;'>Unindicated Modules</h5>"
										."<ul id='list-0-1' year='0' sem='1' class='module-plan-list'>";
									
									foreach($moduleArray[0][1] as $mod) {
										echo "<li id='{$mod['code']}' year='0' sem='1' class='module-item{$mod['style']}'>{$mod['code']}: {$mod['name']}</li>";
									}
									echo "</ul></div>";
									echo '<div id="dialog-message"><div id="dialog-content"></div></div>';
									echo "</p>";
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

						<?php //get_sidebar(); ?>
						<div id="sidebar1" class="sidebar threecol last clearfix" role="complementary">
							
							<div id="change-widget" class="widget" style="display:none;">
								<h4 class="widgettitle"><span class="lwa-title">Changes</span></h4>
								<div id="module-list-changes">
									No changes.
								</div>
								<input id="update-module-btn" type="button" value="Update Modules" class="update-module-btn" style="display:none;"/>
							</div>
							
							<div class="widget">
								<h4 class="widgettitle"><span class="lwa-title">Hide Semesters</span></h4>
								<div id="options">
									<?php
										for($i=1; $i<=4; $i++) {
											for($j=1; $j<=2; $j++) {
												echo '<div>'
													.'<input type="checkbox" id="year'.$i.'sem'.$j.'" name="'.$i.$j.'" class="filterCheckbox" onchange="filterSemester(this)" /> '
													.'Year '.$i.' Sem '.$j.'</div>';
											}
										}
									?>
									<input type="button" value="Clear Filters" onClick="clearFilters()" />
								</div>
								<input id="update-module-btn" type="button" value="Update Modules" style="display:none;"/>
							</div>
							
							<?php if ( is_active_sidebar( 'sidebar1' ) ) : ?>

								<?php dynamic_sidebar( 'sidebar1' ); ?>

							<?php else : ?>

								<!-- This content shows up if there are no widgets defined in the backend. -->

								<div class="alert alert-help">
									<p><?php _e("Please activate some Widgets.", "bonestheme");  ?></p>
								</div>

							<?php endif; ?>

						</div>

				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->

<?php get_footer(); ?>
