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

Copyright 2020-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

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

$base                                 = dirname( __FILE__ );
$includes                             = $base . '/includes';
$iworks_interactive_polish_map_prefix = 'ipm_';

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

function iworks_interactive_polish_map_get_options_object() {
	global $iworks_interactive_polish_map_options, $iworks_interactive_polish_map_prefix;
	if ( is_object( $iworks_interactive_polish_map_options ) ) {
		return $iworks_interactive_polish_map_options;
	}
	$iworks_interactive_polish_map_options = new iworks_options();
	$iworks_interactive_polish_map_options->set_option_function_name( 'iworks_interactive_polish_map_options' );
	$iworks_interactive_polish_map_options->set_option_prefix( $iworks_interactive_polish_map_prefix );
	return $iworks_interactive_polish_map_options;
}

function iworks_interactive_polish_map_options_init() {
	global $iworks_interactive_polish_map_options;
	$iworks_interactive_polish_map_options->options_init();
}

function iworks_interactive_polish_map_activate() {
	global $iworks_interactive_polish_map_prefix;
	$iworks_interactive_polish_map_options = new iworks_options();
	$iworks_interactive_polish_map_options->set_option_function_name( 'iworks_interactive_polish_map_options' );
	$iworks_interactive_polish_map_options->set_option_prefix( $iworks_interactive_polish_map_prefix );
	$iworks_interactive_polish_map_options->activate();
	/**
	 * install tables
	 */
	$iworks_interactive_polish_map = new iworks_interactive_polish_map;
	$iworks_interactive_polish_map->db_install();
}

function iworks_interactive_polish_map_deactivate() {
	global $iworks_interactive_polish_map_options;
	$iworks_interactive_polish_map_options->deactivate();
}

global $iworks_interactive_polish_map;
$iworks_interactive_polish_map = new iworks_interactive_polish_map();

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

return;
/*
Plugin Name:
Plugin URI: http://wordpress.org/extend/plugins/interactive-polish-map/
Description: Interactive Polish Map display Polish map using shortcode or widget.
Version: trunk
Author: Marcin Pietrzak
Author URI: http://iworks.pl
*/

/**
 * $HeadURL: https://plugins.svn.wordpress.org/interactive-polish-map/trunk/interactive-polish-map.php $
 * $LastChangedBy: iworks $
 * $LastChangedDate: 2015-08-08 10:14:03 +0200 (Sat, 08 Aug 2015) $
 */

/**
 * i18n
 */
$mo_file = dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo';
if ( file_exists( $mo_file ) && is_readable( $mo_file ) ) {
	load_textdomain( 'interactive_polish_map', $mo_file );
}

$ipm_data = array(
	'districts' => array(
		'dolnoslaskie'        => 'Województwo Dolnośląskie',
		'kujawsko_pomorskie'  => 'Województwo Kujawsko-Pomorskie',
		'lubelskie'           => 'Województwo Lubelskie',
		'lubuskie'            => 'Województwo Lubuskie',
		'lodzkie'             => 'Województwo Łódzkie',
		'malopolskie'         => 'Województwo Małopolskie',
		'mazowieckie'         => 'Województwo Mazowieckie',
		'opolskie'            => 'Województwo Opolskie',
		'podkarpackie'        => 'Województwo Podkarpackie',
		'podlaskie'           => 'Województwo Podlaskie',
		'pomorskie'           => 'Województwo Pomorskie',
		'slaskie'             => 'Województwo Śląskie',
		'swietokrzyskie'      => 'Województwo Świętokrzyskie',
		'warminsko_mazurskie' => 'Województwo Warmińsko-Mazurskie',
		'wielkopolskie'       => 'Województwo Wielkopolskie',
		'zachodniopomorskie'  => 'Województwo Zachodniopomorskie',
	),
	'menu'      => array(
		'ukryta'               => array(
			'widget' => true,
			'desc'   => __( 'hidden', 'interactive_polish_map' ),
		),
		'po_lewej'             => array(
			'widget' => false,
			'desc'   => __( 'on left', 'interactive_polish_map' ),
		),
		'po_prawej'            => array(
			'widget' => false,
			'desc'   => __( 'on right', 'interactive_polish_map' ),
		),
		'ponizej'              => array(
			'widget' => true,
			'desc'   => __( 'under', 'interactive_polish_map' ),
		),
		'ponizej dwie_kolumny' => array(
			'widget' => false,
			'desc'   => __( 'under - two columns (only for 400px & 500px)', 'interactive_polish_map' ),
		),
		'ponizej trzy_kolumny' => array(
			'widget' => false,
			'desc'   => __( 'under - three columns (only for 500px)', 'interactive_polish_map' ),
		),
	),
	'type'      => array(
		'200' => array(
			'widget' => true,
			'desc'   => '200px',
		),
		'300' => array(
			'widget' => true,
			'desc'   => '300px',
		),
		'400' => array(
			'widget' => false,
			'desc'   => '400px',
		),
		'500' => array(
			'widget' => false,
			'desc'   => '500px',
		),
	),
);



function ipm_produce_radio( $name, $title, $options, $default ) {
	$option_value = get_option( $name, $default );
	$content      = sprintf( '<h3>%s</h3>', $title );
	$content     .= '<ul>';
	$i            = 0;
	foreach ( $options as $value => $data ) {
		$id = $name . $i++;
		if ( isset( $option['name'] ) ) {
			$id = $name . $option['name'] . $i++;
		}
		$content .= sprintf(
			'<li><label for="%s"><input type="radio" name="%s" value="%s"%s id="%s"/> %s</label></li>',
			$id,
			$name,
			$value,
			( $option_value == $value ) ? ' checked="checked"' : '',
			$id,
			$data['desc']
		);
	}
	$content .= '</ul>';
	echo $content;
}

function ipm_settings() {
	global $ipm_data;
	?>
<div class="wrap">
	<h2><?php _e( 'Interactive Polish Map', 'interactive_polish_map' ); ?></h2>
	<form method="post" action="options.php">
	<?php
	ipm_produce_radio( 'ipm_type', __( 'Map width', 'interactive_polish_map' ), $ipm_data['type'], 500 );
	ipm_produce_radio( 'ipm_menu', __( 'Display list', 'interactive_polish_map' ), $ipm_data['menu'], 'ponizej' );
	?>
		<h3><?php _e( 'URL' ); ?></h3>
		<table class="widefat">
	<?php
	$i = 1;
	foreach ( $ipm_data['districts'] as $key => $value ) {
		$url = get_option( 'ipm_districts_' . $key, '' );
		printf(
			'<tr%s><td style="width:150px">%s:</td><td><input type="text" name="ipm_districts_%s" value="%s" class="widefat"/></td></tr>',
			++$i % 2 ? ' class="alternate"' : '',
			$value,
			$key,
			$url
		);
	}
	settings_fields( 'ipm-options' );
	?>
		</table>
		<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'interactive_polish_map' ); ?>" /></p>
	</form>
</div>
	<?php
}


function imp_plugin_links( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( strpos( $plugin_file, basename( __FILE__ ) ) ) {
		$plugin_meta[] = '<a href="options-general.php?page=ipm_settings">' . __( 'Settings' ) . '</a>';
		$plugin_meta[] = '<a href="http://iworks.pl/donate/ipm.php">' . __( 'Donate' ) . '</a>';
	}
	return $plugin_meta;
}

/**
 * load snippets
 */
include_once dirname( __FILE__ ) . '/snippets/widget_map.php';

