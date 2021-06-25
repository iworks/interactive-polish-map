<?php
/*
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

if ( class_exists( 'iworks_interactive_polish_map' ) ) {
	return;
}

require_once dirname( dirname( __FILE__ ) ) . '/iworks.php';

class iworks_interactive_polish_map extends iworks {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_pages' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_shortcode( 'mapa-polski', array( $this, 'shortcode' ) );
	}

	public function admin_init() {
		global $ipm_data;
		foreach ( array_keys( $ipm_data['districts'] ) as $key ) {
			register_setting( 'ipm-options', 'ipm_districts_' . $key, 'wp_filter_nohtml_kses' );
		}
		register_setting( 'ipm-options', 'ipm_type', 'absint' );
		register_setting( 'ipm-options', 'ipm_menu', 'wp_filter_nohtml_kses' );
	}

	public function add_pages() {
		add_submenu_page(
			'options-general.php',
			__( 'Interactive Polish Map', 'interactive_polish_map' ),
			__( 'Interactive Polish Map', 'interactive_polish_map' ),
			'edit_posts',
			'ipm_settings',
			'ipm_settings'
		);
	}

	public function shortcode() {
		global $ipm_data;
		$content = sprintf( '<div id="ipm_type_%d"><ul id="w" class="%s">', get_option( 'ipm_type', 500 ), get_option( 'ipm_menu', 'ponizej' ) );
		$i       = 1;
		foreach ( $ipm_data['districts'] as $key => $value ) {
			$url = get_option( 'ipm_districts_' . $key, '%' );
			if ( ! $url ) {
				$url = '#';
			}
			$content .= sprintf(
				'<li id="w%d"><a href="%s" title="%s">%s</a></li>',
				$i++,
				$url,
				$value,
				$value
			);
		}
		$content .= '</ul></div>';
		return $content;
	}


	public function init() {
		wp_register_script( 'interactive_polish_map', plugins_url( '/js/interactive_polish_map.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'interactive_polish_map' );
		wp_register_style( 'myStyleSheets', plugins_url( '/style/interactive_polish_map.css', __FILE__ ) );
		wp_enqueue_style( 'myStyleSheets' );
		add_filter( 'plugin_row_meta', 'imp_plugin_links', 10, 4 );
	}
}

