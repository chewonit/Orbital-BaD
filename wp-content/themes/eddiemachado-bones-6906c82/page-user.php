<?php
/*
Template Name: User Page
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

									<h1 class="page-title"><?php the_title(); ?></h1>
									
									<!--<p class="byline vcard"><?php
										printf(__('Posted <time class="updated" datetime="%1$s" pubdate>%2$s</time> by <span class="author">%3$s</span>.', 'bonestheme'), get_the_time('Y-m-j'), get_the_time(__('F jS, Y', 'bonestheme')), bones_get_the_author_posts_link());
									?></p>-->


								</header> <!-- end article header -->

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
								</section> <!-- end article section -->
								
								<p>
								<script>
								function manageModule(str) {
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											document.getElementById("module-list").innerHTML=xmlhttp.responseText;
											$("#loading").fadeOut();
											$("#module-list").slideDown();
											$(".edit-box").each(function() {
												var id;
												id = $(this).attr('modid');
												if ($('#modulepreq_txt'+id).val() != "") {
													var preqString = $('#modulepreq_txt'+id).val();
													var arr = preqString.split(",");
													for(i=0; i<arr.length; i++) {
														$("#preq-list"+id).append( "<div class='preq-style preq-tag"+id+"' modid='"+id+"' style='float:left;'>" + arr[i] + "</div>" );
													}
												}
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-functions/?"+str,true);
									xmlhttp.send();
								}
								function checkModule(str, id) {
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											//alert((xmlhttp.responseText).substring(0,2));
											if((xmlhttp.responseText).substring(0,2)=="1=") {
												$modid = (xmlhttp.responseText).substring(2);
												$("#takencheckbox"+$modid).prop("checked", true);
												alert("A module having this as a prerequisite has already been taken! Unable to drop module.");
											} else {
												var arr = xmlhttp.responseText.split(",");
												for(i=0; i<arr.length; i+=2) {
													$("#module-item"+arr[i]).removeClass("module-istaken module-available module-locked").addClass("module-"+arr[i+1]);
													if(arr[i+1] == "locked") {
														$("#takencheckbox"+arr[i]).attr("disabled", true);
													} else {
														$("#takencheckbox"+arr[i]).attr("disabled", false);
													}
												}
											}
											//alert((xmlhttp.responseText).substring(1));	
											$("#loading-overlay").fadeOut(200, function(){
												$("#loading-overlay").removeClass("loading-overlay");
											});
											$("#loading-overlay-message").fadeOut(200, function(){
												$("#loading-overlay-message").removeClass("loading-overlay-message");
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-functions/?"+str,true);
									xmlhttp.send();
								}
								function deleteMod(id) {
									$("#module-list").slideUp(200, function(){
										manageModule("op=delete&ur=<?php echo $username ?>&id="+id);
									});
									$("#loading").fadeIn();
								};
								function editMod(id) {
									$("#edit"+id).slideToggle();
								};
								$(document).ready(function(){
									function popluatePreqList(id) {
										var elems = $(".preq-tag"+id).nextAll(), countPreq = elems.length;
										var preqString = "";
										if($.trim($(".preq-tag"+id).text()).length == 0){
											$("#modulepreq_txt"+id).val("");
										}
										$(".preq-tag"+id).each(function() {
											if( countPreq != 0) {
												preqString += $(this).html() + ",";
											} else {
												preqString += $(this).html();
												$("#modulepreq_txt"+id).val(preqString);
											}
											countPreq --;
										});
									}
									$("#addPreq").click(function(){
										if ( $("#modulepreq2_txt").val() == "") {
											$("#modulepreq2_txt").focus();
											$("#modulepreq2_txt").effect('shake');
										} else {
											$("#preq-list").append( "<div class='preq-style preq-tag' modid='' style='float:left;'>" + $("#modulepreq2_txt").val() + "</div>" );
											$("#modulepreq2_txt").val("");
											//$("#addPreq").attr("disabled", true);
										
											popluatePreqList("");
										}
									});
									$(document).on("click", ".preq-style", function(e) {
										id = $(this).attr('modid');
										$(this).remove();
										popluatePreqList(id);
									});
									$("#insertModBtn").click(function(){
										if ($("#modulecode_txt").val().length != 0 && $("#modulename_txt").val().length != 0) {
											$("#module-list").slideUp(200, function(){
												manageModule("ur=<?php echo $username ?>&mc="+$("#modulecode_txt").val()
													+"&op=insert"
													+"&mn="+$("#modulename_txt").val()
													+"&preq="+$("#modulepreq_txt").val());
													$("#loading").fadeIn();
													$("#modulecode_txt").val("");
													$("#modulename_txt").val("");
													$("#modulepreq_txt").val("");
													$("#modulepreq2_txt").val("");
													$("#preq-list").html("");
											});
											
										} else {
											if ($("#modulecode_txt").val().length == 0) {
												$("#modulecode_txt").effect('shake');
												$("#modulecode_txt").focus();
											} else if ($("#modulename_txt").val().length == 0) {
												$("#modulename_txt").effect('shake');
												$("#modulename_txt").focus();
											}
										}
									});
									function populateEditPreq() {
										$(".edit-box").each(function() {
											var id;
											id = $(this).attr('modid');
											if ($('#modulepreq_txt'+id).val() != "") {
												var preqString = $('#modulepreq_txt'+id).val();
												var arr = preqString.split(",");
												for(i=0; i<arr.length; i++) {
													$("#preq-list"+id).append( "<div class='preq-style preq-tag"+id+"' modid='"+id+"' style='float:left;'>" + arr[i] + "</div>" );
												}
											}
										});
									}
									populateEditPreq();
									$(document).on("click", ".editaddpreqbtn", function(e) {
									//$(".editaddpreqbtn").click(function() {
										id = $(this).attr('modid');
										if ( $("#modulepreq2_txt"+id).val() == "") {
											$("#modulepreq2_txt"+id).focus();
											$("#modulepreq2_txt"+id).effect('shake');
										} else {
											$("#preq-list"+id).append( "<div class='preq-style preq-tag"+id+"' modid='"+id+"' style='float:left;'>" + $("#modulepreq2_txt"+id).val() + "</div>" );
											$("#modulepreq2_txt"+id).val("");
											//$("#addPreq").attr("disabled", true);
										
											popluatePreqList(id);
										}
									});
									$(document).on("click", ".editmodulebtn", function(e) {
										id = $(this).attr('modid');
										if ( $("#editmodulecode_txt"+id).val() == "") {
											$("#editmodulecode_txt"+id).focus();
											$("#editmodulecode_txt"+id).effect('shake');
											return;
										}
										if  ( $("#editmodulename_txt"+id).val() == "") {
											$("#editmodulename_txt"+id).focus();
											$("#editmodulename_txt"+id).effect('shake');
											return;
										}
										$("#module-list").slideUp(200, function(){
											manageModule("ur=<?php echo $username ?>&mc="+$("#editmodulecode_txt"+id).val()
												+"&op=update"
												+"&mn="+$("#editmodulename_txt"+id).val()
												+"&preq="+$("#modulepreq_txt"+id).val()
												+"&id="+id);
										});
										$("#loading").fadeIn();
									});
									$(document).on("click", ".modulecheckbox", function(e) {
										id = $(this).attr('modid');
										if($(this).prop('checked')) { // just checked, module just taken
											// Update database istaken to 1
											checkModule("ur=<?php echo $username ?>&mc="
												+"&op=checkModule"
												+"&cv=1"
												+"&id="+id, id);
											$("#loading-overlay").fadeIn();
											$("#loading-overlay").addClass("loading-overlay");
											$("#loading-overlay-message").fadeIn();
											$("#loading-overlay-message").addClass("loading-overlay-message");
											// update color of module-item in the list
										} else {	// just unchecked, module has been dropped
											// Update database istaken to 0
											checkModule("ur=<?php echo $username ?>&mc="
												+"&op=checkModule"
												+"&cv=0"
												+"&id="+id, id);
											$("#loading-overlay").fadeIn(100);
											$("#loading-overlay").addClass("loading-overlay");
											$("#loading-overlay-message").fadeIn(100);
											$("#loading-overlay-message").addClass("loading-overlay-message");
											// update color of module-item in the list
										}
									});
								});
								</script>
								<form name="input" action="<?php echo the_permalink() ?>" method="get">
									<div>
										<div class="input-module"><div>Module code: </div><div><input id="modulecode_txt" type="text" name="modulecode_txt"></div></div>
										<div class="input-module"><div>Module name: </div><div><input id="modulename_txt" type="text" name="modulename_txt"></div></div>
									</div>
									<br style="clear:both;" />
									<div>
										<div class="input-module">
										<div>Module Prerequisite: </div>
										<div>
											<div style="float:left;"><input id="modulepreq2_txt" type="text" name="modulepreq2_txt"></div>
											<input id="modulepreq_txt" type="text" name="modulepreq_txt" style="display:none">
											<div style="float:left; margin: 0 3px;"><input id="addPreq" type="button" value="Add Prerequisite"></div>
											<div id="preq-list" class="preq-list" style="float:left; margin: 0 3px;"></div>
										</div>
										</div>
									</div>
									<br />
									<div class="input-module-submit">
										<input id="insertModBtn" type="button" value="Add New Module">
										<div id="testdiv"></div>
									</div>
								</form>
								</p>
								
								<?php
									echo '<div id="module-list">';
									
									// count the modules
									$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );
									echo "<p>Total Module Count: {$user_count}</p>";
									
									// Retreive all modules tagged with this user
									$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' ORDER BY $wpdb->moduledata.modulecode" );
									$rawmodule = $wpdb->get_results( $query );
									
									// populate the modules
									foreach($rawmodule as $a) {
										if($a->modulepreq==null) {
											$preq = "nill";
										} else {
										    $preq = $a->modulepreq;
										}
										
										if($a->istaken) {
											echo '<div id="module-item'. $a->id .'" class="module-item module-istaken"><div>';
										} else if($a->status=="available"){
											echo '<div id="module-item'. $a->id .'" class="module-item module-available"><div>';
										} else {
											echo '<div id="module-item'. $a->id .'" class="module-item"><div>';
										}
										print_r('<div style="float:left">' . $a->modulecode . ": " . $a->modulename . "</div>");
										
										// -- Manage module buttons container
										echo '<div style="float:right">';
										
										// Module Delete button
										echo '<div style="float:right">'
											.'<form id="deleteform' . $a->id . '" action="JavaScript:deleteMod('. $a->id .')">'
											//.'<form name="deleteform' . $a->id . '" action="' . get_permalink() . '" method="get">'
											.'<input id="id_txt' . $a->id . '" type="text" name="id_txt" style="display:none;" value="' . $a->id . '">'
											.'<input id="deletebutton" type="submit" value="Delete" style="display:inline">'
											.'</form></div>';
											
										// Module Edit button
										echo '<div style="float:right">'
											.'<form id="editform' . $a->id . '" action="JavaScript:editMod('. $a->id .')">'
											.'<input id="editid_txt" type="text" name="editid_txt" style="display:none;" value="' . $a->id . '">'
											.'<input id="editbutton" type="submit" value="Edit" style="display:inline">'
											.'</form></div>';
											
										// Module Taken button
										echo '<div style="float:right">'
											.'<form id="takenform' . $a->id . '" action="JavaScript:alert('. $a->id .')">'
											.'<input id="takenid_txt" type="text" name="takenid_txt" style="display:none;" value="' . $a->id . '">';
										if($a->istaken) {
											echo 'Module Taken: <input id="takencheckbox' . $a->id . '" modid="' . $a->id . '" class="modulecheckbox" type="checkbox" value="Module Taken" style="display:inline" checked>&nbsp;';
										} else if($a->status=="available"){
											echo 'Module Taken: <input id="takencheckbox' . $a->id . '" modid="' . $a->id . '" class="modulecheckbox" type="checkbox" value="Module Taken" style="display:inline">&nbsp;';
										} else {
											echo 'Module Taken: <input id="takencheckbox' . $a->id . '" modid="' . $a->id . '" class="modulecheckbox" type="checkbox" value="Module Taken" style="display:inline" disabled="true">&nbsp;';
										}
										echo '</form></div>';
										
										echo '</div>';
										// -- End of Manage module buttons container
									
										echo "<br style='clear:both;'/></div>";
										
										// More module details
										echo '<div style="float:left; margin-right:10px;">Level: '. $a->level .'000</div>'
											.'<div style="float:left">Prerequisite: '. $preq . '</div>';
										echo '<div style="clear:both;"></div>';
										
										echo '<div class="edit-box" id="edit' . $a->id . '" modid="' . $a->id . '" style="margin: 10px; background-color:white; display:none;">'
											.'<form name="input" action="'. get_permalink() .'" method="get">'
											.'<div>'
											.'<div class="input-module"><div>Module code: </div><div><input id="editmodulecode_txt' . $a->id . '" type="text" name="editmodulecode_txt' . $a->id . '" value="' . $a->modulecode . '"></div></div>'
											.'<div class="input-module"><div>Module name: </div><div><input id="editmodulename_txt' . $a->id . '" type="text" name="editmodulename_txt' . $a->id . '" value="' . $a->modulename . '"></div></div>'
											.'</div>'
											.'<br style="clear:both;" />'
											.'<div>'
											.'<div class="input-module"><div>Module Prerequisite: </div>'
											.'<div>'
											.'<div style="float:left;"><input id="modulepreq2_txt' . $a->id . '" type="text" name="modulepreq2_txt' . $a->id . '"></div>'
											.'<input id="modulepreq_txt' . $a->id . '" type="text" name="modulepreq_txt' . $a->id . '" value="' . $a->modulepreq . '" style="display:none">'
											.'<div style="float:left; margin: 0 3px;"><input class="editaddpreqbtn" modid="' . $a->id . '" id="editaddPreq' . $a->id . '" type="button" value="Add Prerequisite"></div>'
											.'<div id="preq-list' . $a->id . '" class="preq-list" style="float:left; margin: 0 3px;"></div>'
											.'</div></div>'
											.'<input id="updateid_txt' . $a->id . '" type="text" name="updateid_txt' . $a->id . '" style="display:none" value="' . $a->id . '">'
											.'</div>'
											.'<br style="clear:both;" />'
											.'<div class="input-module-submit"><input modid="' . $a->id . '" class="editmodulebtn" id="editmodulebtn' . $a->id . '" type="button" value="Update"></div></form>'
											.'</div>';
										
										// -- End of module item
										echo '</div>';
									}
									// -- End of module-list
									echo '</div>';
									echo '<center><div id="loading" style="display:none; background-image: url(\''. get_template_directory_uri() .'/library/images/loading.gif\'); width:150px; height:150px;"></div></center>';
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
