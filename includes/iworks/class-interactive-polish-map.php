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

	private $provinces;
	private $options;
	private $blocks;
	private $capability;
	private $settings_page;

	public function __construct() {
		parent::__construct();
		/**
		 * Settings
		 */
		$this->options    = iworks_interactive_polish_map_get_options_object();
		$this->base       = dirname( dirname( __FILE__ ) );
		$this->dir        = basename( dirname( $this->base ) );
		$this->version    = 'PLUGIN_VERSION';
		$this->capability = apply_filters( 'interactive_polish_map_capability', 'manage_options' );
		/**
		 * WordPress Hooks
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		/**
		 * WordPress Shortcodes
		 */
		add_shortcode( 'mapa-polski', array( $this, 'shortcode' ) );
		add_shortcode( 'interactive-polish-map', array( $this, 'shortcode' ) );
		/**
		 * iWorks Rate Class
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
		add_filter( 'iworks_rate_settings_page_url_' . 'interactive-polish-map', array( $this, 'filter_get_setting_page_url' ) );
	}

	public function shortcode() {
		/**
		 * get map
		 */
		$file = dirname( $this->base ) . '/assets/images/map-of-poland.svg';
		ob_start();
		include $file;
		$map = ob_get_clean();
		if ( empty( $map ) ) {
			return;
		}
		/**
		 * border color
		 */
		$border_color = $this->options->get_option( 'border' );
		$map          = preg_replace( '/"#ddd"/', $border_color, $map );
		/**
		 * provinces colors
		 */
		$style          = $this->options->get_option( 'style' );
		$color1         = $this->options->get_option( 'color1' );
		$color1_opacity = intval( $this->options->get_option( 'color1_opacity' ) ) / 100;

		switch ( $style ) {
			case 'own':
				if ( 1 > $color1_opacity ) {
					$color1 = $this->hex2rgba( $color1, $color1_opacity );
				}
				$map = preg_replace( '/"#75c5f0"/', $color1, $map );
				$map = preg_replace( '/"#9ae095"/', $color1, $map );
				$map = preg_replace( '/"#fed979"/', $color1, $map );
				$map = preg_replace( '/"#fffa5e"/', $color1, $map );
				$map = preg_replace( '/"#f9cdf4"/', $color1, $map );
				break;
			case 'auto':
				$range     = 6;
				$color1    = $this->hex2hsl( $color1 );
				$color1[2] = max( ( 2 * $range ), min( ( 100 - 2 * $range ), $color1[2] ) );
				$color     = sprintf( '"hsl( %d, %d%%, %d%% )"', $color1[0], $color1[1], $color1[2] );
				$map       = preg_replace( '/"#75c5f0"/', $color, $map );
				$color     = sprintf( '"hsl( %d, %d%%, %d%% )"', $color1[0], $color1[1], $color1[2] + $range );
				$map       = preg_replace( '/"#9ae095"/', $color, $map );
				$color     = sprintf( '"hsl( %d, %d%%, %d%% )"', $color1[0], $color1[1], $color1[2] + ( 2 * $range ) );
				$map       = preg_replace( '/"#fed979"/', $color, $map );
				$color     = sprintf( '"hsl( %d, %d%%, %d%% )"', $color1[0], $color1[1], $color1[2] - $range );
				$map       = preg_replace( '/"#fffa5e"/', $color, $map );
				$color     = sprintf( '"hsl( %d, %d%%, %d%% )"', $color1[0], $color1[1], $color1[2] - ( 2 * $range ) );
				$map       = preg_replace( '/"#f9cdf4"/', $color, $map );
				break;
		}
		/**
		 * styles
		 */
		$styles = array(
			sprintf( 'width:%dpx', $this->options->get_option( 'size' ) ),
			sprintf( 'height:%dpx', $this->options->get_option( 'size' ) ),
		);

		$map     = sprintf(
			'<div class="interactive_polish_map-map" styles="%s">%s</div>',
			esc_attr( implode( ';', $styles ) ),
			$map
		);
		$content = sprintf(
			'<div id="ipm_type_%d">%s<ul id="w" class="%s">',
			get_option( 'ipm_type', 500 ),
			$map,
			get_option( 'ipm_menu', 'ponizej' )
		);
		/**
		 * list
		 */
		$provinces = $this->get_provinces();
		$i         = 1;
		foreach ( $provinces as $key => $value ) {
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

	public function admin_init() {
		iworks_interactive_polish_map_options_init();
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function init() {
		wp_register_script( 'interactive_polish_map', plugins_url( '/js/interactive_polish_map.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'interactive_polish_map' );
		wp_register_style( 'myStyleSheets', plugins_url( '/style/interactive_polish_map.css', __FILE__ ) );
		wp_enqueue_style( 'myStyleSheets' );
	}

	/**
	 * cache provinces codes
	 *
	 * @since 2.0.0
	 */
	private function get_provinces() {
		if ( empty( $this->provinces ) ) {
			$this->provinces = $this->options->get_group( 'provinces' );
		}
		return $this->provinces;
	}

	/**
	 * Plugin logo for rate messages
	 *
	 * @since 2.0.0
	 * @since 1.0.5
	 *
	 * @param string $logo Logo, can be empty.
	 * @param object $plugin Plugin basic data.
	 */
	public function filter_plugin_logo( $logo, $plugin ) {
		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}
		if ( 'interactive-polish-map' === $plugin['slug'] ) {
			return plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '/assets/images/logo.svg';
		}
		return $logo;
	}

	/**
	 * get settings page
	 *
	 * @since 2.0.0
	 */
	public function filter_get_setting_page_url( $url ) {
		return $this->get_setting_page_url();
	}

	/**
	 * get settings page url
	 *
	 * @since 2.0.0
	 */
	private function get_setting_page_url() {
		return add_query_arg( 'page', 'ipm_index', admin_url( 'themes.php' ) );
	}

	private function rgb2array( $color ) {
		$default = array( 0, 0, 0 );
		$colors  = array();
		$color   = preg_replace( '/#/', '', strtolower( $color ) );
		if ( strlen( $color ) == 6 ) {
			$colors = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		}
		if ( strlen( $color ) == 3 ) {
			$colors = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		}
		if ( ! empty( $colors ) ) {
			return $colors;
		}
		return $default;
	}

	private function hex2rgba( $color, $opacity = false ) {
		$default = 'rgb(0,0,0)';
		//Return default if no color provided
		if ( empty( $color ) ) {
			return $default;
		}
		$rgb = array_map( 'hexdec', $this->rgb2array( $color ) );
		//Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}
		//Return rgb(a) color string
		return $output;
	}

	public function hex2hsl( $color ) {
		$rgb         = array_map( 'hexdec', $this->rgb2array( $color ) );
		$color_red   = $rgb[0] / 255;
		$color_green = $rgb[1] / 255;
		$color_blue  = $rgb[2] / 255;
		$max         = max( $color_red, $color_green, $color_blue );
		$min         = min( $color_red, $color_green, $color_blue );
		$hue;
		$saturation;
		$lightness = ( $max + $min ) / 2;
		$d         = $max - $min;
		if ( $d == 0 ) {
			$hue = $saturation = 0; // achromatic
		} else {
			$saturation = $d / ( 1 - abs( 2 * $lightness - 1 ) );
			switch ( $max ) {
				case $color_red:
					$hue = 60 * fmod( ( ( $color_green - $color_blue ) / $d ), 6 );
					if ( $color_blue > $g ) {
						$hue += 360;
					}
					break;
				case $color_green:
					$hue = 60 * ( ( $color_blue - $color_red ) / $d + 2 );
					break;
				case $color_blue:
					$hue = 60 * ( ( $color_red - $color_green ) / $d + 4 );
					break;
			}
		}

		return array( round( $hue, 2 ), 100 * round( $saturation, 2 ), 100 * round( $lightness, 2 ) );
	}

}

