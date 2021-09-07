<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://nextab.de
 * @since             1.0.0
 * @package           nxt_css_toggle
 *
 * @wordpress-plugin
 * Plugin Name:       CSS Powered Toggle
 * Plugin URI:        https://nextab.de
 * Description:       This plugin allows you to use an element in the WordPress library as a toggle (e.g. a button) to trigger the visibility of another element
 * Version:           1.0.0
 * Author:            nexTab - Oliver Gehrmann
 * Author URI:        https://nextab.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nxt-css-toggle
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function nxt_enqueue_css_toggle_styles() {
	wp_enqueue_style( 'nxt_css_toggle', plugin_dir_url( __FILE__ ) . 'css/css_toggle_styles.css', array(), '1.0.0', 'all' );
}
add_action('wp_enqueue_scripts', 'nxt_enqueue_css_toggle_styles');

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'NXT_CSS_TOGGLE', '1.0.0' );

// This function grabs the content of a post that is referenced via its ID and outputs it.
function inhalt_shortcode( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'id' => '',
		'wrapper' => 'nein',
	), $atts );
	if($a['id'] == '') return "Fehler - es wurde keine ID angegeben!";
	if($a['wrapper'] == 'nein') return do_shortcode(str_replace("et_pb_text_", "nxt_text_", get_post_field('post_content', $a['id'])));
	else return '<div class="nxt_content_container">' . do_shortcode(str_replace("et_pb_text_", "nxt_text_",get_post_field('post_content', $a['id']))) . '</div>';
}
add_shortcode( 'inhalt', 'inhalt_shortcode' );

/* This function takes the first module and wraps it inside a label; it then adds an input right before the 2nd specified module so you can trigger the module via CSS

Usage:
1.) Create a module in Divi that you would like to use as the trigger element (e.g. a button) and save it to library
2.) Create the element that should be displayed once you click on your trigger.
3.) Use the shortcode and reference the post IDs to build your button trigger

Example: [blurb-trigger blurb="295" content="298"]

The optional parameter "wrapper" allows you to wrap the entire content inside a div if you want.
*/
function blurb_trigger_shortcode( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'blurb' => '295',
		'content' => '298',
		'table' => 'nein',
		'wrapper' => 'nein',
	), $atts );
	$nxt_reference_atts["id"] = $a['blurb'];
	// $return_string = '<!-- nxt_reference_atts[id]'
	$return_string = '';
	if($a["wrapper"] == "ja") { $return_string .= '<div class="nxt_blurb_trigger_container">'; }
	$return_string .= '<label class="nxt-trigger-label" for="content_' . $a["content"] . '">';
	// $return_string .= do_shortcode('[inhalt id="'.$a["blurb"].'"]);
	$return_string .= inhalt_shortcode($nxt_reference_atts);
	$return_string .= '</label><input id="content_'.$a["content"].'" type="checkbox" name="content_'.$a["content"].'" class="nxt-trigger-input" /><div class="nxt_input_trigger_container">';
	$nxt_reference_atts["id"] = $a['content'];
	$return_string .= ($a["table"] == "ja") ? do_shortcode("[wptb id=" . $a['content'] . "]") : inhalt_shortcode($nxt_reference_atts);
	$return_string .= '</div>';
	if($a["wrapper"] == "ja") { $return_string .= '</div>'; }
	$return_string = str_replace("et_pb_text_", "nxt_text_", $return_string);
	return $return_string;
}
add_shortcode( 'blurb-trigger', 'blurb_trigger_shortcode' );