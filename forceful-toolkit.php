<?php

/*
Plugin Name: Forceful Toolkit
Plugin URI: http://kopatheme.com
Description: A specific plugin use in Forceful Lite Theme to generate shortcodes, add specific widgets and allow user rate the posts.
Version: 1.0.1
Author: Kopatheme
Author URI: http://kopatheme.com
License: GPLv3

Forceful Toolkit plugin, Copyright 2015 Kopatheme.com
Forceful Toolkit is distributed under the terms of the GNU GPL
*/

define('FT_PATH', plugin_dir_path(__FILE__));

add_action('plugin_loaded','forceful_toolkit_init');
add_action('after_setup_theme', 'forceful_toolkit_after_setup_theme');

function forceful_toolkit_init(){
    load_plugin_textdomain( 'forceful-toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

function forceful_toolkit_after_setup_theme() {
	if (!class_exists('Kopa_Framework'))
		return;
	require plugin_dir_path( __FILE__ ) . 'util.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-flexslider.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-articles-list.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-articles-list-thumb.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-advertising.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-gallery.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-mailchimp-subscribe.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-feedburner-subscribe.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-combo.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-lastest-comments.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-flickr.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-socials.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-weather.php';
	require plugin_dir_path( __FILE__ ) . 'widgets/widget-twitter.php';

	if (is_admin()) {
		add_filter('user_contactmethods', 'modify_contact_methods');
	}else{
		add_filter('widget_text', 'do_shortcode');
	}

}

function modify_contact_methods($profile_fields) {

    // Add new fields
	$profile_fields['twitter']     = esc_attr__( 'Twitter URL', 'forceful-toolkit');
	$profile_fields['facebook']    = esc_attr__( 'Facebook URL', 'forceful-toolkit');
	$profile_fields['feedurl']     = esc_attr__( 'Feed URL', 'forceful-toolkit');
	$profile_fields['google-plus'] = esc_attr__( 'Google+ URL', 'forceful-toolkit');
	$profile_fields['flickr']      = esc_attr__( 'Flickr URL', 'forceful-toolkit');

    return $profile_fields;
}

/*
 * Enqueue script and style
 */
 require plugin_dir_path( __FILE__ ) . 'enqueue.php';

/*
 * Register shortcodes
 */
require plugin_dir_path( __FILE__ ) . 'shortcodes.php';

/*
 * Register Rating
 */

require plugin_dir_path( __FILE__ ) . 'post-rating.php';