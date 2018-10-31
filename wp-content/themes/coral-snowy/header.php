<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package coral-snowy
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<meta property="og:image" content="http://vber.ou.edu.vn/files/2017/12/og-VBER-1.png" />

<?php wp_head(); ?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-111642378-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-111642378-1');
</script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
  <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'coral-snowy' ); ?></a>
  <div id="gcontainer-header">
	<header id="masthead" class="flex space wrap" role="banner">
		<div style="padding-bottom: 20px;">
			<div class="flex center">
				<!--OU logo-->
				<div style="border-right: 2px solid #00305E; padding-right: 5px !important;">
					<a href="http://www.ou.edu.vn" target="_blank" rel="ou.edu.vn">
					<img style="height:80px; width:140px" src="<?php echo get_site_url();?>/wp-content/uploads/2017/12/OU-1.png">
					</a>
					<div class="logo-text">
						<div>HO CHI MINH CITY</div>
						<div>OPEN UNIVERSITY</div>
					</div>
				</div>
				<div style="padding-left: 5px; align-self: flex-end">
					<a href="<?php echo get_site_url();?>/about">
						<img style="max-height: 50px;" src="<?php echo get_site_url();?>/wp-content/uploads/2017/12/BERG-1.png">
					</a>
				</div>
			</div>
		</div><!-- .site-branding -->
		
		<div>
			<div style="display: flex;justify-content: center;">
				<!--logo-->
				<div>
					<a href="<?php echo get_site_url();?>">
					<img style="height: 104px;width: 360px;" src="http://vber.ou.edu.vn/files/2018/10/VBER2019.png">
					</a>
				</div>
				<div style="padding-left: 20px;line-height: 16px;">
					<div class="vber-title1">VIETNAM'S</div>
					<div class="vber-title2" style="padding-top: 12px">BUSINESS & ECONOMICS RESEARCH CONFERENCE</div>
					<div class="vber-title3" style="padding-top: 8px; font-size:125%">
						<a href="http://www.caravellehotel.com" target="_blank">Caravelle Saigon, Vietnam</a>
					</div>
					<div class="vber-title3" style="padding-top: 6px;">19-23 Lam Son Square, District 1, Ho Chi Minh City,  Vietnam</div>
					<div class="vber-title3">18<sup>th</sup>-20<sup>th</sup> July 2019</div>
				</div>
			</div>
		</div><!-- .site-branding -->
		
		<div class="vber-title4" style="width:100%">
		VIETNAM'<span style="color: #00305E;">s</span><div style="color: #00305E;">PLACE IN THE ASIA PACIFIC REGION</div>
		</div><!-- .site-branding -->
	</header><!-- #masthead -->
	
	<div id="navi" class="grid-parent grid-100 tablet-grid-100 mobile-grid-100">
		<nav id="site-navigation" class="main-navigation egrid" role="navigation">
			<i id="menu-button" class="fa fa-bars collapsed"><span><?php _e( '  Menu', 'coral-snowy' ); ?></span></i>
			<?php 
			if (!is_rtl()) {
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'main-menu', 'menu_class' => 'sm sm-clean collapsed' ) );
				} else {
					wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb'  => 'coral_snowy_wp_page_menu_mine' ) ); 
				}
			} else {
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'main-menu', 'menu_class' => 'sm sm-rtl sm-clean collapsed' ) );
				} else {
					wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb'  => 'coral_snowy_wp_page_menu_mine' ) );
				}	
			}
			?>
		</nav><!-- #site-navigation -->
	</div><!-- #navi -->	

	<?php do_action( 'coral_snowy_slider' ); ?>
	
	<?php if (is_front_page()) { ?>
	<div style="padding-top: 15px; padding-bottom: 8px; background-color: #5bc6cc; border-bottom: 5px solid #FD5F00">
		<div class="grid-container flex center align-center">
			<div class="" style="padding-right: 20px;">
				<div style="color: #fff;font-size: 160%;font-weight: bold; line-height: 24px">Join the Conference</div>
				<div style="color: #fff;text-align: right">Conference starts in</div>
			</div>
			<div class="" style="padding-right: 50px;">
			<?php echo do_shortcode('[ujicountdown id="event" expire="2018/07/22 16:00" hide="true" url="" subscr="" recurring="" rectype="second" repeats=""]'); ?>
			</div>
		</div>
	</div>
	<?php } ?>
  </div>
  
  <div id="gcontainer" class="grid-container">
	<!-- breadcrumbs from Yoast or NavXT plugins -->
	<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
	<div id="breadcrumbs" class="grid-parent grid-100 tablet-grid-100 mobile-grid-100">
		<div class="breadcrumbs grid-100 tablet-grid-100 mobile-grid-100">
			<?php yoast_breadcrumb(); ?>
		</div>
	</div>
	<?php elseif (function_exists('bcn_display')) : ?>
	<div id="breadcrumbs" class="grid-parent grid-100 tablet-grid-100 mobile-grid-100">
		<div class="breadcrumbs grid-100 tablet-grid-100 mobile-grid-100" xmlns:v="http://rdf.data-vocabulary.org/#">
			<?php bcn_display(); ?>
		</div>
	</div>
	<?php endif; ?>
	
	<div id="content" class="site-content grid-parent grid-100 tablet-grid-100 mobile-grid-100">
