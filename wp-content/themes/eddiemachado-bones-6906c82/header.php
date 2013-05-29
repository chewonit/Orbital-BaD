<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<!-- Google Chrome Frame for IE -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php wp_title(''); ?></title>

		<!-- mobile meta (hooray!) -->
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<!-- icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) -->
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<!-- or, set /favicon.ico for IE10 win -->
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		
		<link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Chewy' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Trade+Winds|Sirin+Stencil|Righteous' rel='stylesheet' type='text/css'>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

		<meta name="google-site-verification" content="175XpthK0RP-h2g_1EqM4ps2GrWWniI7MUJCUet40dc" />
		
		<!-- wordpress head functions -->
		<?php wp_head(); ?>
		<!-- end of wordpress head -->

		<!-- drop Google Analytics Here -->
		<!-- end analytics -->

	</head>

	<body <?php body_class(); ?>>
		<div id="loading-overlay"><div id="loading-overlay-message"><center><h1>loading</h1><img src="<?php echo get_template_directory_uri(); ?>/library/images/loader.gif"></center></div></div>
		<div id="container">
		
			<header class="header" role="banner">

				<div id="inner-header" class="wrap clearfix">

					<!-- to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> -->
					<p id="logo" class="h1"><a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a></p>

					<!-- if you'd like to use the site description you can un-comment it below -->
					<div id="description" style="margin-left: 10px;"><?php bloginfo('description'); ?></div>

					<script>
					$(function() {
						// Clickable Dropdown
						$('.click-nav > ul').toggleClass('no-js js');
						$('.click-nav .js ul').hide();
						$('.click-nav .js').click(function(e) {
							$('.click-nav .js ul').slideToggle(200);
							$('.clicker').toggleClass('active');
							e.stopPropagation();
						});
						$(document).click(function() {
							if ($('.click-nav .js ul').is(':visible')) {
								$('.click-nav .js ul', this).slideUp();
								$('.clicker').removeClass('active');
							}
						});
					});
					</script>
					<nav role="navigation">
						<?php bones_main_nav(); ?>
					</nav>
					<div class="click-nav">
						<ul class="no-js">
							<li>
								<a class="clicker"><img height=15 src="<?php echo get_template_directory_uri(); ?>/library/images/nav-arrow.png" />  Navigation</a>
								<?php bones_main_nav(); ?>
							</li>
						</ul>
					</div>
					
				</div> <!-- end #inner-header -->

			</header> <!-- end header -->
