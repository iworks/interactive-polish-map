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
	/**
	 * names
	 *
	 * @since 2.0.0
	 */
	private $provinces_names;
	/**
	 * legacy settings, from version less than 2.0.0
	 *
	 * @since 2.0.0
	 */
	private $legacy = array(
		'lower-silesia'     => 'dolnoslaskie',
		'kuyavia-pomerania' => 'kujawsko_pomorskie',
		'lublin'            => 'lubelskie',
		'lubusz'            => 'lubuskie',
		'lodzkie'           => 'lodzkie',
		'lesser-poland'     => 'malopolskie',
		'masovia'           => 'mazowieckie',
		'opole'             => 'opolskie',
		'subcarpathia'      => 'podkarpackie',
		'podlaskie'         => 'podlaskie',
		'pomerania'         => 'pomorskie',
		'swietokrzyskie'    => 'slaskie',
		'silesia'           => 'swietokrzyskie',
		'warmia-masuria'    => 'warminsko_mazurskie',
		'greater-poland'    => 'wielkopolskie',
		'west-pomerania'    => 'zachodniopomorskie',
	);

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
		 * provinces names
		 */
		$this->provinces_names = array(
			'lower-silesia'     => __( 'Lower-Silesia', 'interactive-polish-map' ),
			'kuyavia-pomerania' => __( 'Kuyavia-Pomerania', 'interactive-polish-map' ),
			'lublin'            => __( 'Lublin', 'interactive-polish-map' ),
			'lubusz'            => __( 'Lubusz', 'interactive-polish-map' ),
			'lodzkie'           => __( 'Lodzkie', 'interactive-polish-map' ),
			'lesser-poland'     => __( 'Lesser-Poland', 'interactive-polish-map' ),
			'masovia'           => __( 'Masovia', 'interactive-polish-map' ),
			'opole'             => __( 'Opole', 'interactive-polish-map' ),
			'subcarpathia'      => __( 'Subcarpathia', 'interactive-polish-map' ),
			'podlaskie'         => __( 'Podlaskie', 'interactive-polish-map' ),
			'pomerania'         => __( 'Pomerania', 'interactive-polish-map' ),
			'swietokrzyskie'    => __( 'Swietokrzyskie', 'interactive-polish-map' ),
			'silesia'           => __( 'Silesia', 'interactive-polish-map' ),
			'warmia-masuria'    => __( 'Warmia-Masuria', 'interactive-polish-map' ),
			'greater-poland'    => __( 'Greater-Poland', 'interactive-polish-map' ),
			'west-pomerania'    => __( 'West-Pomerania', 'interactive-polish-map' ),
		);
		/**
		 * WordPress Hooks
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'register_assets' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
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
		/**
		 * iWorks Interactive Polish Map
		 */
		add_filter( 'iworks_interactive_polish_map_menu_legacy', array( $this, 'convert_legacy_menu_value' ) );
	}

	public function register_widgets() {
		require_once dirname( __FILE__ ) . '/interactive-polish-map/class-interactive-polish-map-widget.php';
		register_widget( 'InteractivePolishMapWidget' );
	}

	public function shortcode( $atts ) {
		$args         = shortcode_atts(
			array(
				'id'    => 0,
				'menu'  => null,
				'style' => null,
			),
			$atts
		);
		$args['menu'] = $this->convert_legacy_menu_value( $args['menu'] );
		/**
		 * settings
		 */
		$class_base = 'interactive-polish-map';
		$classes    = array(
			$class_base,
		);
		/**
		 * provinces list UL
		 */
		if ( empty( $args['menu'] ) ) {
			$args['menu'] = $this->options->get_option( 'menu' );
		}
		$args['menu'] = $this->convert_legacy_menu_value( $args['menu'] );
		$classes[]    = sprintf( '%s-%s', $class_base, $args['menu'] );
		if ( preg_match( '/^after\-/', $args['menu'] ) ) {
			$classes[] = sprintf( '%s-%s', $class_base, 'after' );
		}
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
		$style = $this->options->get_option( 'style' );
		$color = $this->options->get_option( 'color' );
		switch ( $style ) {
			case 'own':
				$map = preg_replace( '/"#75c5f0"/', $color, $map );
				$map = preg_replace( '/"#9ae095"/', $color, $map );
				$map = preg_replace( '/"#fed979"/', $color, $map );
				$map = preg_replace( '/"#fffa5e"/', $color, $map );
				$map = preg_replace( '/"#f9cdf4"/', $color, $map );
				break;
			case 'auto':
				$range    = 6;
				$color    = $this->hex2hsl( $color );
				$color[2] = max( ( 2 * $range ), min( ( 100 - 2 * $range ), $color[2] ) );
				$color1   = sprintf( '"hsl( %d, %d%%, %d%% )"', $color[0], $color[1], $color[2] );
				$map      = preg_replace( '/"#75c5f0"/', $color1, $map );
				$color1   = sprintf( '"hsl( %d, %d%%, %d%% )"', $color[0], $color[1], $color[2] + $range );
				$map      = preg_replace( '/"#9ae095"/', $color1, $map );
				$color1   = sprintf( '"hsl( %d, %d%%, %d%% )"', $color[0], $color[1], $color[2] + ( 2 * $range ) );
				$map      = preg_replace( '/"#fed979"/', $color1, $map );
				$color1   = sprintf( '"hsl( %d, %d%%, %d%% )"', $color[0], $color[1], $color[2] - $range );
				$map      = preg_replace( '/"#fffa5e"/', $color1, $map );
				$color1   = sprintf( '"hsl( %d, %d%%, %d%% )"', $color[0], $color[1], $color[2] - ( 2 * $range ) );
				$map      = preg_replace( '/"#f9cdf4"/', $color1, $map );
				break;
		}
		/**
		 * styles
		 */
		$styles = array();
		if ( empty( $args['style'] ) ) {
			$styles[] = sprintf( 'max-width:%dpx', $this->options->get_option( 'size' ) );
		} else {
			$styles[] = sprintf( 'max-width:%dpx', $args['style'] );
		}
		$map = sprintf(
			'<div class="%s-map" style="%s">%s</div>',
			esc_attr( $class_base ),
			esc_attr( implode( ';', $styles ) ),
			$map
		);
		/**
		 * legacy list
		 */
		$list = '';
		if ( 'hide' !== $args['menu'] && 0 === $args['id'] ) {
			$list .= sprintf( '<ul class="%s-menu">', $class_base );
			$i     = 1;
			foreach ( $this->legacy as $key => $legacy_name ) {
				$url   = get_option( 'ipm_districts_' . $legacy_name, sprintf( '#%s', $key ) );
				$list .= sprintf(
					'<li><a href="%s" title="%s" data-target="%s">%s</a></li>',
					esc_attr( $url ),
					esc_attr( $this->provinces_names[ $key ] ),
					esc_html( $key ),
					esc_html( $this->provinces_names[ $key ] )
				);
				$re    = sprintf( '/href="#%s"/', $key );
				$value = sprintf( 'href="%s"', esc_attr( $url ) );
				$map   = preg_replace( $re, $value, $map );
			}
			$list .= '</ul>';
		}
		/**
		 * glue it all
		 */
		$content = sprintf( '<div class="%s">', esc_attr( implode( ' ', $classes ) ) );
		if ( 'before' === $args['menu'] ) {
			$content .= $list;
		}
		$content .= $map;
		if ( 'before' !== $args['menu'] ) {
			$content .= $list;
		}
		$content .= '</div>';
		return $content;
	}

	public function admin_init() {
		iworks_interactive_polish_map_options_init();
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * register styles
	 *
	 * @since 2.0.0
	 */
	public function register_assets() {
		wp_register_style(
			$this->options->get_option_name( 'frontend' ),
			sprintf( plugins_url( '/assets/styles/frontend%s.css', $this->base ), $this->dev ? '' : '.min' ),
			array(),
			$this->version
		);
		$file    = plugins_url( '/blocks/map/editor.js', $this->base );
		$handler = 'ipm-block-map-editor';
		wp_register_script(
			$handler,
			$file,
			array( 'wp-editor', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
			$this->version
		);
		wp_set_script_translations(
			$handler,
			'interactive-polish-map',
			dirname( $this->base ) . '/languages/'
		);

	}

	public function enqueue_assets() {
		wp_enqueue_style( $this->options->get_option_name( 'frontend' ) );
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
					if ( $color_blue > $color_green ) {
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

	/**
	 * Legacy menu position
	 */
	public function convert_legacy_menu_value( $position ) {
		$legacy = array(
			'ukryta'               => 'hide',
			'po_lewej'             => 'left',
			'po_prawej'            => 'right',
			'ponizej'              => 'after',
			'ponizej dwie_kolumny' => 'after-two-columns',
			'ponizej trzy_kolumny' => 'after-three-columns',
		);
		if ( isset( $legacy[ $position ] ) ) {
			return $legacy[ $position ];
		}
		return $position;
	}

}

