<?php
/*
 * Plugin Name:       Interactive Polish Map
 * Plugin URI:        http://iworks.pl/interactive-polish-map/
 * Description:       PLUGIN_DESCRIPTION
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Version:           PLUGIN_VERSION
 * Author:            Marcin Pietrzak
 * Author URI:        http://iworks.pl/
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       interactive-polish-map
 * Domain Path:       /languages
 *

Copyright 2013-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$base     = dirname( __FILE__ );
$includes = $base . '/includes';

/**
 * require: Iworksinteractive-polish-map Class
 */
if ( ! class_exists( 'iworks_interactive_polish_map' ) ) {
	require_once $includes . '/iworks/class-interactive-polish-map.php';
}
/**
 * configuration
 */
require_once $base . '/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $includes . '/iworks/options/options.php';
}
/**
 * i18n
 */
load_plugin_textdomain( 'interactive-polish-map', false, plugin_basename( $base ) . '/languages' );

/**
 * load options
 */

global $iworks_interactive_polish_map_options;
$iworks_interactive_polish_map_options = iworks_interactive_polish_map_get_options_object();

/**
 * run plugin core
 */
global $iworks_interactive_polish_map;
$iworks_interactive_polish_map = new iworks_interactive_polish_map();

/**
 * load blocks
 */
require_once $base . '/blocks/map.php';


function iworks_interactive_polish_map_get_options_object() {
	global $iworks_interactive_polish_map_options;
	if ( is_object( $iworks_interactive_polish_map_options ) ) {
		return $iworks_interactive_polish_map_options;
	}
	$iworks_interactive_polish_map_options = new iworks_options();
	$iworks_interactive_polish_map_options->set_option_function_name( 'iworks_interactive_polish_map_options' );
	$iworks_interactive_polish_map_options->set_option_prefix( 'ipm_' );
	return $iworks_interactive_polish_map_options;
}

function iworks_interactive_polish_map_options_init() {
	global $iworks_interactive_polish_map_options;
	$iworks_interactive_polish_map_options->options_init();
}

function iworks_interactive_polish_map_activate() {
	$iworks_interactive_polish_map_options = new iworks_options();
	$iworks_interactive_polish_map_options->set_option_function_name( 'iworks_interactive_polish_map_options' );
	$iworks_interactive_polish_map_options->set_option_prefix( 'ipm_' );
	$iworks_interactive_polish_map_options->activate();
}

function iworks_interactive_polish_map_deactivate() {
	global $iworks_interactive_polish_map_options;
	$iworks_interactive_polish_map_options->deactivate();
}

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'iworks_interactive_polish_map_activate' );
register_deactivation_hook( __FILE__, 'iworks_interactive_polish_map_deactivate' );
/**
 * Ask for vote
 */
include_once $includes . '/iworks/rate/rate.php';
do_action(
	'iworks-register-plugin',
	plugin_basename( __FILE__ ),
	__( 'Interactive Polish Map', 'interactive-polish-map' ),
	'interactive-polish-map'
);

