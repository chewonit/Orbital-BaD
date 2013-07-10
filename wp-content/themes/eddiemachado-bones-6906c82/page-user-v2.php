<?php
/*
Template Name: User Page v2
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
								$(function() {
									$( "#dialog" ).dialog({
										width:'auto',
										height:'auto',
										autoOpen: false,
										show: {
										effect: "fade",
										duration: 400
										},
										hide: {
										effect: "explode",
										duration: 400
										}
									});
									$( "#modulePopUpDialog" ).dialog({
										width:'auto',
										height:'auto',
										modal: true,
										autoOpen: false,
										show: {
										effect: "fade",
										duration: 400
										},
										hide: {
										effect: "explode",
										duration: 400
										}
									});
								});
								/*
									Function to clear tooltip and prerequisite info
								*/
								function clearTooltip() {
									$("#retreivedpreq").slideUp();
									$('#modulepreq2_txt').prop('title', '');
									$( "#dialog" ).dialog( "close" );
								}
								/*
									Function to Get Module Info from NUSmods
								*/
								function getModuleInfo(str) {
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											//document.getElementById("widget-modulecount").innerHTML=xmlhttp.responseText;
											var xmlDoc = $.parseXML( xmlhttp.responseText ),
											$xml = $( xmlDoc );
											if($xml.find( "title" ).text() != "") { 
												$('#modulename_txt').val($xml.find( "title" ).text());
												
												if($xml.find( "preq" ).text() != "") {
													$('#retreivedpreq').text('Retrevied data: ' + $xml.find( "preq" ).text());
												} else {
													$('#retreivedpreq').text('Retrevied data: Nil');
												}
												$('#modulepreq2_txt').prop('title', 'Copy the relavant prerequisite codes and add them here');
												$("#modulepreq2_txt").tooltip();
												$(".ui-tooltip").css('margin-top', "-10 px !important");
												$('#retreivedpreq').slideDown();
												$('#modulecredit_txt').val($xml.find( "mc" ).text());
												$('#getModuleIvle').attr('src', 'http://ivle7.nus.edu.sg/lms/Account/NUSBulletin/msearch_view_full.aspx?modeCode='+$xml.find( "code" ).text().toLowerCase())
												$( "#dialog" ).dialog( "open" );
											} else {
												alert('Unable to find module');
											}
											$("#loading-overlay").fadeOut(200, function(){
												$("#loading-overlay").removeClass("loading-overlay");
											});
											$("#loading-overlay-message").fadeOut(200, function(){
												$("#loading-overlay-message").removeClass("loading-overlay-message");
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/nusmods/?"+str,true);
									xmlhttp.send();
								}
								/*
									Function to Count Modules
								*/
								function countModules() {
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											document.getElementById("widget-modulecount").innerHTML=xmlhttp.responseText;
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-count/?ur=<?php echo $username; ?>",true);
									xmlhttp.send();
								}
								/*
									Function to Manage Modules
									Insert, Delete, Update
									Takes in the get parameters to send to php script.
								*/
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
											//alert(xmlhttp.responseText);
											document.getElementById("module-list").innerHTML=xmlhttp.responseText;
											$("#loading").fadeOut();
											$("#module-list").slideDown();
											filterModules();
											countModules();
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
											$(function() {
												$( "input[type=submit], input[type=button]" )
													.button()
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-functions-2/?"+str,true);
									xmlhttp.send();
								}
								/*
									Function to mark modules as taken or not taken
									Takes in the get parameters to send to php script.
								*/
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
												alert("There exist a module that has this as a prerequisite has already been taken! Unable to drop target module.");
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
											filterModules();
											// Clears the loading overlay animation
											$("#loading-overlay").fadeOut(200, function(){
												$("#loading-overlay").removeClass("loading-overlay");
											});
											$("#loading-overlay-message").fadeOut(200, function(){
												$("#loading-overlay-message").removeClass("loading-overlay-message");
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-functions-2/?"+str,true);
									xmlhttp.send();
								}
								/*
									Function to Flush and Load template course structure
									Takes in the get parameters to send to php script.
								*/
								function flushModules(str) {
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											//alert(xmlhttp.responseText);
											document.getElementById("module-list").innerHTML=xmlhttp.responseText;
											$("#loading").fadeOut();
											$("#module-list").slideDown();
											countModules();
											filterModules();
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
												$(function() {
													$( "input[type=submit], input[type=button]" )
														.button()
												});
											});
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/flush-modules/?"+str,true);
									xmlhttp.send();
								}
								/*
									Function is called when the flush module buttons are clicked.
								*/
								function flushModuleBtn(course) {
									// Slides up the whole list of modules and calls for manage module function
									// operation = delete
									$("#module-list").slideUp(200, function(){
										flushModules("ur=<?php echo $username ?>&course="+course);
									});
									$("#loading").fadeIn();
									$("html, body").animate({ scrollTop: 0 }, "slow");
								};
								/*
									Function is called when the delete button on the module item is clicked.
								*/
								function deleteMod(id) {
									// Slides up the whole list of modules and calls for manage module function
									// operation = delete
									$("#module-list").slideUp(200, function(){
										manageModule("op=delete&ur=<?php echo $username ?>&id="+id);
									});
									$("#loading").fadeIn();
									$("html, body").animate({ scrollTop: 0 }, "slow");
								};
								/*
									Function toggle the visibility of the edit plane embeded in the module item.
								*/
								function editMod(id) {
									$("#edit"+id).slideToggle();
								};
								/*
									Function to create pop up when module name is click
								*/
								function modulepopup(modid) {
									var xmlhttp;
									if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
										xmlhttp=new XMLHttpRequest();
									}
									else {// code for IE6, IE5
										xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
									}
									xmlhttp.onreadystatechange=function() {
										if (xmlhttp.readyState==4 && xmlhttp.status==200) {
											$('#modulePopUpContent').html(xmlhttp.responseText);
											$('#modulePopUpDialog').dialog('open');
											
											// Clears the loading overlay animation
											$("#loading-overlay").fadeOut(200, function(){
												$("#loading-overlay").removeClass("loading-overlay");
											});
											$("#loading-overlay-message").fadeOut(200, function(){
												$("#loading-overlay-message").removeClass("loading-overlay-message");
											});
											
											/*
											$("#popup-overlay-innermessage").html('<div style="padding: 10px;">'+xmlhttp.responseText+'</div>');
											tempHeight = $("#popup-overlay-innermessage").height();
											$("#popup-overlay-innermessage").css('display', 'none');
											$("#popup-overlay-innermessage").height(0);
											$("#popuploading").slideUp(200, function() {
												$("#popup-overlay-innermessage").css('display', 'block');
												$("#popup-overlay-innermessage").animate({ 
													height: tempHeight+'px',
												}, 1500 );
											});
											*/
										}
									}
									xmlhttp.open("GET","<?php echo get_home_url(); ?>/module-popup/?ur=<?php echo $username ?>&mc="+modid,true);
									xmlhttp.send();
									
									$('#modulePopUpDialog').dialog('option', 'title', modid);
									
									$("#loading-overlay").fadeIn(100);
									$("#loading-overlay").addClass("loading-overlay");
									$("#loading-overlay-message").fadeIn(100);
									$("#loading-overlay-message").addClass("loading-overlay-message");
									
									/*
									$("#popup-overlay").fadeIn();
									$("#popup-overlay").addClass("popup-overlay");
									$("#popup-overlay-container").fadeIn();
									$("#popup-overlay-container").addClass("popup-overlay-container");
									$("#popup-overlay-message").fadeIn();
									$("#popup-overlay-message").addClass("popup-overlay-message");
									
									$("#popup-overlay-message").html('<div id="popuploading"><center><h1>loading</h1><img src="<?php echo get_template_directory_uri(); ?>/library/images/loader.gif"></center></div>'
													+'<div id="popup-overlay-innermessage"></div>'
													+'<div style="padding: 0 10px 10px 10px; text-align:right;">'
													+'<a href="Javascript:removemodulepopup()">close window</a></div>');
									*/
								}
								/*
									Function to remove pop up when module name is click
								*/
								function removemodulepopup() {
									$("#popup-overlay").fadeOut(200, function(){
										$("#popup-overlay").removeClass("popup-overlay");
									});
									$("#popup-overlay-container").fadeOut(200, function(){
										$("#popup-overlay-container").removeClass("popup-overlay-container");
									});
									$("#popup-overlay-message").fadeOut(200, function(){
										$("#popup-overlay-message").removeClass("popup-overlay-message");
									});
								}
								/*
									Function to filter the modules
								*/
								function filterModules() {
									if( $('#checkbox-availablemodules').prop('checked')) {
										$('.module-item.module-available').each(function(){
											$(this).addClass('filtered-out');
											$(this).slideUp();
										});
									} else {
										$('.module-item.module-available').each(function(){
											$(this).removeClass('filtered-out');
											$(this).slideDown();
										});
									}
									if( $('#checkbox-clearedmodules').prop('checked')) {
										$('.module-item.module-istaken').each(function(){
											$(this).addClass('filtered-out');
											$(this).slideUp();
										});
									} else {
										$('.module-item.module-istaken').each(function(){
											$(this).removeClass('filtered-out');
											$(this).slideDown();
										});
									}
									if( $('#checkbox-lockedmodules').prop('checked')) {
										$('.module-item').each(function(){
											if( !($(this).hasClass('module-istaken') || $(this).hasClass('module-available')) ) {
												$(this).addClass('filtered-out');
												$(this).slideUp();
											}
										});
									} else {
										$('.module-item').each(function(){
											if( !($(this).hasClass('module-istaken') || $(this).hasClass('module-available')) ) {
												$(this).removeClass('filtered-out');
												$(this).slideDown();
											}
										});
									}
									if( $('#checkbox-level1000').prop('checked')) {
										$(".module-item-level-1").each(function(){
											$(this).parent().parent().parent().addClass('leveled-out');
											$(this).parent().parent().parent().slideUp();
										});
									} else {
										$(".module-item-level-1").each(function(){
											if( $(this).parent().parent().parent().hasClass('leveled-out')) {
												$(this).parent().parent().parent().removeClass('leveled-out');
												if( !($(this).parent().parent().parent().hasClass('filtered-out'))) {
													$(this).parent().parent().parent().slideDown();
												}
											}
										});
									}
									if( $('#checkbox-level2000').prop('checked')) {
										$(".module-item-level-2").each(function(){
											$(this).parent().parent().parent().addClass('leveled-out');
											$(this).parent().parent().parent().slideUp();
										});
									} else {
										$(".module-item-level-2").each(function(){
											if( $(this).parent().parent().parent().hasClass('leveled-out')) {
												$(this).parent().parent().parent().removeClass('leveled-out');
												if( !($(this).parent().parent().parent().hasClass('filtered-out'))) {
													$(this).parent().parent().parent().slideDown();
												}
											}
										});
									}
									if( $('#checkbox-level3000').prop('checked')) {
										$(".module-item-level-3").each(function(){
											$(this).parent().parent().parent().addClass('leveled-out');
											$(this).parent().parent().parent().slideUp();
										});
									} else {
										$(".module-item-level-3").each(function(){
											if( $(this).parent().parent().parent().hasClass('leveled-out')) {
												$(this).parent().parent().parent().removeClass('leveled-out');
												if( !($(this).parent().parent().parent().hasClass('filtered-out'))) {
													$(this).parent().parent().parent().slideDown();
												}
											}
										});
									}
									if( $('#checkbox-level4000').prop('checked')) {
										$(".module-item-level-4").each(function(){
											$(this).parent().parent().parent().addClass('leveled-out');
											$(this).parent().parent().parent().slideUp();
										});
									} else {
										$(".module-item-level-4").each(function(){
											if( $(this).parent().parent().parent().hasClass('leveled-out')) {
												$(this).parent().parent().parent().removeClass('leveled-out');
												if( !($(this).parent().parent().parent().hasClass('filtered-out'))) {
													$(this).parent().parent().parent().slideDown();
												}
											}
										});
									}
								}
								/*
									Function to clear all the module filters
								*/
								function clearModuleFilters() {
									$('#checkbox-availablemodules').prop('checked', false);
									$('#checkbox-clearedmodules').prop('checked', false);
									$('#checkbox-lockedmodules').prop('checked', false);
									$('#checkbox-level1000').prop('checked', false);
									$('#checkbox-level2000').prop('checked', false);
									$('#checkbox-level3000').prop('checked', false);
									$('#checkbox-level4000').prop('checked', false);
									$(".module-item").each(function(){
										$(this).removeClass('filtered-out leveled-out');
										$(this).slideDown();
									});
								}
								$(document).ready(function(){
									/*
										Function consolidates all the prerequisite 
										tags and puts them into a string.
										Stores prerequisite string in designated textbox.
									*/
									function popluatePreqList(id) {
										// gets all of the graphical prerequisite tags
										var elems = $(".preq-tag"+id).nextAll(), countPreq = elems.length;
										var preqString = "";
										// if no prerequisites
										if($.trim($(".preq-tag"+id).text()).length == 0){
											$("#modulepreq_txt"+id).val("");
										}
										// run through all the prerequisite tags and add
										// each tag text to the storage string
										$(".preq-tag"+id).each(function() {
											if( countPreq != 0) {
												preqString += $(this).html() + ",";
											} else {
												// this is the last tag to be accessed
												preqString += $(this).html();
												$("#modulepreq_txt"+id).val(preqString);
											}
											countPreq --;
										});
									}
									/*
										This button is the add prerequisite button
										for the create new module form
									*/
									$("#addPreq").click(function(){
										if ( $("#modulepreq2_txt").val() == "") {
											// textbox empty
											$("#modulepreq2_txt").focus();
											$("#modulepreq2_txt").effect('shake');
										} else {
											// add the graphical prerequisite tag
											$("#preq-list").append( "<div class='preq-style preq-tag' modid='' style='float:left;'>" + $("#modulepreq2_txt").val() + "</div>" );
											$("#modulepreq2_txt").val("");
											// execute function to update the prerequisite storage string
											popluatePreqList("");
										}
									});
									/*
										Attach a click listener to each prerequisite tag.
										As the tags are dynamically created by jquery
										the 'on' function is need.
									*/
									$(document).on("click", ".preq-style", function(e) {
										// get the embedded id on the tag and remove the tag
										id = $(this).attr('modid');
										$(this).remove();
										// update the prerequisite storage string
										popluatePreqList(id);
									});
									/*
										Function to insert a new module
										calls manage module function with operation=insert
									*/
									$("#insertModBtn").click(function(){
										// check if required fields are met
										if ($("#modulecode_txt").val().length != 0 && $("#modulename_txt").val().length != 0) {
											$("#module-list").slideUp(200, function(){
												manageModule("ur=<?php echo $username ?>&mc="+$("#modulecode_txt").val()
													+"&op=insert"
													+"&mn="+$("#modulename_txt").val()
													+"&preq="+$("#modulepreq_txt").val()
													+"&credit="+$("#modulecredit_txt option:selected").val()
													+"&grade="+$("#modulegrade_txt option:selected").index()
													+"&yr="+$("#modulereadyear_txt option:selected").val()
													+"&sem="+$("#modulereadsem_txt option:selected").val()
												);
												clearTooltip();
												$("#loading").fadeIn();
												$("#modulecode_txt").val("");
												$("#modulename_txt").val("");
												$("#modulepreq_txt").val("");
												$("#modulepreq2_txt").val("");
												$("#modulecredit_txt option:eq(0)").prop('selected', true);
												$("#modulegrade_txt option:eq(0)").prop('selected', true);
												$("#modulereadyear_txt option:eq(0)").prop('selected', true);
												$("#modulereadsem_txt option:eq(0)").prop('selected', true);
												$("#preq-list").html("");
											});
										} else {
											// form validation
											if ($("#modulecode_txt").val().length == 0) {
												$("#modulecode_txt").effect('shake');
												$("#modulecode_txt").focus();
											} else if ($("#modulename_txt").val().length == 0) {
												$("#modulename_txt").effect('shake');
												$("#modulename_txt").focus();
											}
										}
									});
									/*
										Function breaks the prerequisite string
										and decodes it to populate the graphical
										prerequisite tags.
									*/
									function populateEditPreq() {
										// For each edit-box which is embedded in each module item.
										$(".edit-box").each(function() {
											// get the embedded id
											var id;
											id = $(this).attr('modid');
											// if the module has prerequisites
											if ($('#modulepreq_txt'+id).val() != "") {
												// break and decode the prerequisite string separated by commas
												var preqString = $('#modulepreq_txt'+id).val();
												var arr = preqString.split(",");
												// populate the graphical tags
												for(i=0; i<arr.length; i++) {
													$("#preq-list"+id).append( "<div class='preq-style preq-tag"+id+"' modid='"+id+"' style='float:left;'>" + arr[i] + "</div>" );
												}
											}
										});
									}
									populateEditPreq();
									/*
										Attach a click listener to each add prerequisite button in the edit module.
										As the edit modules are dynamically created by jquery, the 'on' function is need.
									*/
									$(document).on("click", ".editaddpreqbtn", function(e) {
										// get the embedded id
										id = $(this).attr('modid');
										if ( $("#modulepreq2_txt"+id).val() == "") {
											// form validation
											$("#modulepreq2_txt"+id).focus();
											$("#modulepreq2_txt"+id).effect('shake');
										} else {
											// add the graphical prerequisite tag
											$("#preq-list"+id).append( "<div class='preq-style preq-tag"+id+"' modid='"+id+"' style='float:left;'>" + $("#modulepreq2_txt"+id).val() + "</div>" );
											$("#modulepreq2_txt"+id).val("");
											// update the prerequisite storage string
											popluatePreqList(id);
										}
									});
									/*
										Attach a click listener to each edit module button in the edit module.
										As the edit modules are dynamically created by jquery, the 'on' function is need.
									*/
									$(document).on("click", ".editmodulebtn", function(e) {
										// get the embedded id
										id = $(this).attr('modid');
										// form validation
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
										// slide up the whole module list and call manage module function to update module
										$("#module-list").slideUp(200, function(){
											manageModule("ur=<?php echo $username ?>&mc="+$("#editmodulecode_txt"+id).val()
												+"&op=update"
												+"&mn="+$("#editmodulename_txt"+id).val()
												+"&preq="+$("#modulepreq_txt"+id).val()
												+"&id="+id
												+"&credit="+$("#editmodulecredit_txt"+id+" option:selected").val()
												+"&grade="+$("#editmodulegrade_txt"+id+" option:selected").index()
												+"&yr="+$("#editmodulereadyear_txt"+id+" option:selected").val()
												+"&sem="+$("#editmodulereadsem_txt"+id+" option:selected").val()
											);
										});
										$("#loading").fadeIn();
									});
									/*
										Attach a click listener to module taken checkbox.
									*/
									$(document).on("click", ".modulecheckbox", function(e) {
										// get embedded id
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
									/*
										Attach a click listener to the sort button dropdown list.
									*/
									$("#sort_btn").click(function() {
										// slide up the whole module list and call manage module function to sort module
										$("#module-list").slideUp(200, function(){
											manageModule("ur=<?php echo $username ?>&op=ordermodule"
												+"&order="+$("#sortby").val().toLowerCase().replace(/ /g, ''));
										});
										$("#loading").fadeIn();
										$("html, body").animate({ scrollTop: 0 }, "slow");
									});
									/*
										Attach a click listener to the filter button.
									*/
									$("#filter_btn").click(function() {
										filterModules();
										$("html, body").animate({ scrollTop: 0 }, "slow");
									});
									/*
										Attach a click listener to the filter button.
									*/
									$("#clearfilter_btn").click(function() {
										clearModuleFilters();
									});
									/* 
										Retrevie module info from NUSmods
									*/
									$("#getModInfo").click(function(){
										if ( $("#modulecode_txt").val() == "") {
											// textbox empty
											$("#modulecode_txt").focus();
											$("#modulecode_txt").effect('shake');
										} else {
											getModuleInfo(
												"mc=" + $("#modulecode_txt").val().toUpperCase()
											);
											clearTooltip();
											$("#loading-overlay").fadeIn(100);
											$("#loading-overlay").addClass("loading-overlay");
											$("#loading-overlay-message").fadeIn(100);
											$("#loading-overlay-message").addClass("loading-overlay-message");											
										}
									});
								});
								</script>
								
								<form name="input" action="<?php echo the_permalink() ?>" method="get">
									<div>
										<div class="input-module"><div><strong>Module code: </strong></div>
											<div style="float:left; margin-right: 4px;"><input id="modulecode_txt" type="text" name="modulecode_txt"></div>
											<div style="float:left;"><input id="getModInfo" type="button" value="Get Module Info"></div>
										</div>
									</div>
									<br style="clear:both;" />
									<div>
										<div class="input-module"><div><strong>Module name: </strong></div><div><input id="modulename_txt" type="text" name="modulename_txt"></div></div>
									</div>
									<br style="clear:both;" />
									<div>
										<div class="input-module">
										<div><strong>Module Prerequisite: </strong></div>
										<div id="retreivedpreq"></div>
										<div>
											<div style="float:left;"><input id="modulepreq2_txt" type="text" name="modulepreq2_txt"></div>
											<input id="modulepreq_txt" type="text" name="modulepreq_txt" style="display:none">
											<div style="float:left; margin: 0 4px;"><input id="addPreq" type="button" value="Add Prerequisite"></div>
											<div id="preq-list" class="preq-list" style="float:left; margin: 0 3px;"></div>
										</div>
										</div>
									</div>
									<br style="clear:both;" />
									<div style="margin-top: 8px;">
										<div class="input-module">
											<label for="modulecredit_txt"><strong>Modular Credits: </strong></label>
											<select id="modulecredit_txt">
												<option value="0">0</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="8">8</option>
												<option value="12">12</option>
											</select>
										</div>
										<div class="input-module">
											<label for="modulegrade_txt"><strong>Grade: </strong></label>
											<select id="modulegrade_txt">
												<option value="0">N/A</option>
												<option value="1">A+</option>
												<option value="2">A</option>
												<option value="3">A-</option>
												<option value="4">B+</option>
												<option value="5">B</option>
												<option value="6">B-</option>
												<option value="7">C+</option>
												<option value="8">C</option>
												<option value="9">D+</option>
												<option value="10">D</option>
												<option value="11">F</option>
											</select>
										</div>
									</div>
									<br style="clear:both;" />
									<div style="margin-top: 8px;">
										<div class="input-module">
											<label for="modulereadyear_txt"><strong>Read module in: </strong>Year </label>
											<select id="modulereadyear_txt">
												<option value="0">N/A</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
											</select>
										</div>
										<div class="input-module">
											<label for="modulereadsem_txt">Semester</label>
											<select id="modulereadsem_txt">
												<option value="1">1</option>
												<option value="2">2</option>
											</select>
										</div>
									</div>
									<br style="clear:both;" />
									<div class="input-module-submit">
										<input id="insertModBtn" type="button" value="Add New Module">
										<div id="testdiv"></div>
									</div>
								</form>
								
								<div class="module-colorcode" style="background-color:transparent; font-weight:bold;">Color coding key:</div>
								<div class="module-colorcode module-istaken">Module Cleared</div>
								<div class="module-colorcode module-available">Module Available</div>
								<div class="module-colorcode">Module Locked</div>
								<div style="clear:both"></div>
								
								<!-- Dialog Boxes -->
								<div id="dialog" title="Module Info">
									<iframe id="getModuleIvle" frameborder="0"></iframe>
								</div>
								<div id="modulePopUpDialog">
									<div id="modulePopUpContent"></div>
								</div>
								<!-- End Dialog Boxes -->
								</p>
								
								<?php
									$moduleorder = "modulecode";
									// Get Module Order Prefrence
									if (!isset($wpdb->usermoduledata)) {
										$wpdb->usermoduledata = $table_prefix . 'usermoduledata';
									}
									$check_pref_exist = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->usermoduledata WHERE $wpdb->usermoduledata.username='{$username}'" );
									if($check_pref_exist != 0){
										// Get module order preference
										$query = $wpdb->prepare( "SELECT $wpdb->usermoduledata.moduleorder FROM $wpdb->usermoduledata WHERE $wpdb->usermoduledata.username='{$username}' ");
										$rawresults = $wpdb->get_results( $query );
										foreach($rawresults as $a) {
											$moduleorder = $a->moduleorder;
										}
										if($moduleorder == 'availability') {
											$moduleorder = "istaken,$wpdb->moduledata.status,$wpdb->moduledata.modulecode";
										}
									}
									if (!isset($wpdb->moduledata)) {
										$wpdb->moduledata = $table_prefix . 'moduledata';
									}
								
									echo '<div id="module-list">';
									
									// count the modules
									$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );
									$lvl_count = $wpdb->get_var( "SELECT SUM($wpdb->moduledata.mc) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.level='1'" );
									$mc_count = $wpdb->get_var( "SELECT SUM($wpdb->moduledata.mc) FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}'" );
									//echo "<p>Total Module Count: {$user_count}</p>";
									
									// Calculate CAP 
									$query = $wpdb->prepare( "SELECT $wpdb->moduledata.mc, $wpdb->moduledata.grade FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' AND $wpdb->moduledata.mc!=0 AND $wpdb->moduledata.grade!=0");
									$rawmodule = $wpdb->get_results( $query );
									$totalmccap = 0.00; $tempsum = 0.00; $cumulativecap = number_format((float)0.00, 2, '.', '');
									$gradearray = array(0, 5.0, 4.5, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0);
									foreach($rawmodule as $a) {
										$totalmccap += $a->mc;
										$tempsum += ($a->mc)*($gradearray[$a->grade]);
									}
									if($tempsum > 0) {$cumulativecap = number_format((float)$tempsum/$totalmccap, 2, '.', '');}
									
									// Retreive all modules tagged with this user
									$query = $wpdb->prepare( "SELECT * FROM $wpdb->moduledata WHERE $wpdb->moduledata.username='{$username}' ORDER BY $wpdb->moduledata.{$moduleorder}" );
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
										echo '<div style="float:left">';
										print_r('<div style="font-weight:bold;"><a href="Javascript:modulepopup(\''. $a->modulecode .'\')">' 
											. $a->modulecode . ": " . $a->modulename . "</a></div>");
										// More module details
										echo '<div style="float:left; margin-right:10px;" class="module-item-level-' . $a->level . '">Level: '. $a->level .'000</div>'
											.'<div style="float:left">Prerequisite: '. $preq . '</div>';
										echo '<div style="clear:both;"></div>';
										echo '</div>';
										
										echo '<hr class="module-item-controls-bar" />';
										
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
										
										echo '<div class="edit-box" id="edit' . $a->id . '" modid="' . $a->id . '">'
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
											.'<br style="clear:both;" />'
											.'<input id="updateid_txt' . $a->id . '" type="text" name="updateid_txt' . $a->id . '" style="display:none" value="' . $a->id . '">'
											.'</div>'
											.'<div style="margin-top: 8px;">'
											.'<div class="input-module">'
											.'<label for="editmodulecredit_txt' . $a->id . '">Module Credits: </label>
											<select id="editmodulecredit_txt' . $a->id . '">
												<option value="0">0</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="8">8</option>
												<option value="12">12</option>
											</select><script>$("#editmodulecredit_txt' . $a->id . '").val("'.$a->mc.'");</script>'
											.'</div>'
											.'<div class="input-module">'
											.'<label for="editmodulegrade_txt' . $a->id . '">Grade: </label>
											<select id="editmodulegrade_txt' . $a->id . '">
												<option value="0">N/A</option>
												<option value="1">A+</option>
												<option value="2">A</option>
												<option value="3">A-</option>
												<option value="4">B+</option>
												<option value="5">B</option>
												<option value="6">B-</option>
												<option value="7">C+</option>
												<option value="8">C</option>
												<option value="9">D+</option>
												<option value="10">D</option>
												<option value="11">F</option>
											</select><script>$("#editmodulegrade_txt' . $a->id . '").prop("selectedIndex", '.$a->grade.');</script>'
											.'</div>'
											.'</div>'
											.'<br style="clear:both;" />'
											.'<div style="margin-top: 8px;">'
											.'<div class="input-module">'
											.'<label for="editmodulereadyear_txt' . $a->id . '">Read module in: Year </label>
											<select id="editmodulereadyear_txt' . $a->id . '">
												<option value="0">N/A</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
											</select><script>$("#editmodulereadyear_txt' . $a->id . '").val("'.$a->year.'");</script>'
											.'</div>'
											.'<div class="input-module">'
											.'<label for="editmodulereadsem_txt' . $a->id . '">Semester </label>
											<select id="editmodulereadsem_txt' . $a->id . '">
												<option value="1">1</option>
												<option value="2">2</option>
											</select><script>$("#editmodulereadsem_txt' . $a->id . '").val("'.$a->sem.'");</script>'
											.'</div>'
											.'</div>'
											.'<br style="clear:both;" />'
											.'<div class="input-module-submit"><input modid="' . $a->id . '" class="editmodulebtn" id="editmodulebtn' . $a->id . '" type="button" value="Update"></div></form>'
											.'</div>';
										
										// -- End of module item
										echo '</div>';
									}
									// -- End of module-list
									echo '</div>';
									echo '<center><div id="loading" style="display:none; position:relative !important; background-image: url(\''. get_template_directory_uri() .'/library/images/loading.gif\'); width:150px; height:150px;"></div></center>';
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
							<div class="widget">
								<h4 class="widgettitle"><span class="lwa-title">Stats</span></h4>
								<div id="widget-modulecount">
									Total Module Count: <?php echo $user_count ?><br />
									Level 1 MC Count: <?php echo $lvl_count ?><br />
									Total MC Count: <?php echo $mc_count ?><br />
									Cumulative CAP: <?php echo $cumulativecap ?>
								</div>
							</div>
							<div class="widget">
								<h4 class="widgettitle"><span class="lwa-title">Sort Modules</span></h4>
								<div class="ui-widget">
									<select id="combobox">
										<?php
											switch ($moduleorder) {
												case "modulecode":
													echo '<option value="modulecode" selected>Module Code</option>'
														.'<option value="modulename">Module Name</option>'
														.'<option value="availability">Availability</option>';
													break;
												case "modulename":
													echo '<option value="modulecode">Module Code</option>'
														.'<option value="modulename" selected>Module Name</option>'
														.'<option value="availability">Availability</option>';
													break;
												case "istaken,$wpdb->moduledata.status,$wpdb->moduledata.modulecode":
													echo '<option value="modulecode">Module Code</option>'
														.'<option value="modulename">Module Name</option>'
														.'<option value="availability" selected>Availability</option>';
													break;
											}										
										?>
									</select>
									<input type="button" id="sort_btn" value="Sort" />
								</div>
							</div>
							<div class="widget">
								<h4 class="widgettitle"><span class="lwa-title">Filter Out Modules</span></h4>
								<div class="ui-widget">
									<form id="filter-modules-form">
										<h5 style="margin:0;">Module Availability</h5>
										<input type="checkbox" id="checkbox-availablemodules"> Available Modules<br />
										<input type="checkbox" id="checkbox-lockedmodules"> Locked Modules<br />
										<input type="checkbox" id="checkbox-clearedmodules"> Cleared Modules<br />
										<h5 style="margin:0;">Module Level</h5>
										<input type="checkbox" id="checkbox-level1000"> Level 1000<br />
										<input type="checkbox" id="checkbox-level2000"> Level 2000<br />
										<input type="checkbox" id="checkbox-level3000"> Level 3000<br />
										<input type="checkbox" id="checkbox-level4000"> Level 4000<br />
										<input type="button" id="filter_btn" value="Filter" />
										<input type="button" id="clearfilter_btn" value="Clear Filters" />
									</form>
								</div>
							</div>
							<div class="widget">
								<h4 class="widgettitle"><span class="lwa-title">Flush and Load Presets</span></h4>
								<div>
									Clears all modules in account and loads in a template structure of the selected course.
									<ul>
										<li><a id="cmrequirements_btn" href="JavaScript:flushModuleBtn('cmrequirements')">Communications and Media</a></li>
										<li><a id="csrequirements_btn" href="JavaScript:flushModuleBtn('csrequirements')">Computer Science</a></li>
										<li><a id="isrequirements_btn" href="JavaScript:flushModuleBtn('isrequirements')">Information Systems</a></li>
										<li><a id="ecrequirements_btn" href="JavaScript:flushModuleBtn('ecrequirements')">Electronic Commerce</a></li>
									</ul>
								</div>
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
