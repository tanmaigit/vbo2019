<?php
/*
Plugin Name: Uji&#32;Countdown
Plugin URI: http://www.wpmanage.com/uji-countdown
Description: HTML5 Customizable Countdown.
Version: 2.0.8
Text Domain: uji-countdown
Domain Path: /lang
Author: Wpmanage.com
Author URI: http://wpmanage.com
License: GPLv2
Copyright 2017  WPmanage  (email : info@wpmanage.com)
*/

// If this file is called directly, abort.
defined( 'WPINC' ) || exit;

define( 'UJIC_NAME', 'Uji Countdown' );
define( 'UJIC_VERS', '2.0.8' );
define( 'UJIC_FOLD', 'uji-countdown' );
define( 'UJICOUNTDOWN', trailingslashit( dirname(__FILE__) ) );
define( 'UJICOUNTDOWN_URL', plugin_dir_url( __FILE__ ) );
define( 'UJICOUNTDOWN_BASE', plugin_basename(__FILE__) );
define( 'UJICOUNTDOWN_FILE', __FILE__ );

//Google Fonts
require_once( plugin_dir_path( __FILE__ ) . 'assets/googlefonts.php' );

// Classes
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-uji-countdown-admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-uji-countdown.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-uji-countdown-front.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/class-uji-widget.php' );

// INIT
Uji_Countdown::get_instance();
