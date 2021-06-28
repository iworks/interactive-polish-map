<?php
class InteractivePolishMapWidget extends WP_Widget {

	private $options;

	/** constructor */
	function __construct() {
		parent::__construct(
			__CLASS__,
			$name = __( 'Interactive Polish Map', 'interactive_polish_map' ),
			array(
				'description' => __( 'Widget is used to place interactive polish map.', 'interactive_polish_map' ),
				'classname'   => 'interactive_polish_map',
			)
		);
		/**
		 * Settings
		 */
		$this->options = iworks_interactive_polish_map_get_options_object();
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		/**
		 * cache id for wp_cache object
		 */
		$cache_id = 'InteractivePolishMapWidget' . $args['widget_id'];
		/**
		 * content
		 */
		$content = wp_cache_get( $cache_id, 'InteractivePolishMapWidget' );
		$content = false;
		if ( $content === false ) {
			extract( $args );
			$title    = apply_filters( 'widget_title', $instance['title'] );
			$content  = $before_widget;
			$content .= $before_title . $title . $after_title;
			$content .= do_shortcode(
				sprintf(
					'[interactive-polish-map style="%s" menu="%s"]',
					$instance['type'],
					$instance['menu']
				)
			);
			$content .= $after_widget;
		}
		wp_cache_set( $cache_id, $content, 'InteractivePolishMapWidget', 1800 );
		echo $content;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		foreach ( array( 'title', 'type', 'menu' ) as $key ) {
			$instance[ $key ] = strip_tags( $new_instance[ $key ] );
		}
		return $instance;
	}

	/** @see WP_Widget::form */
	public function form( $instance ) {
		/**
		 * title
		 */
		printf(
			'<p><label for="%s">%s <input class="widefat" id="%s" name="%s" type="text" value="%s" /></label></p>',
			$this->get_field_id( 'title' ),
			__( 'Title:' ),
			$this->get_field_id( 'title' ),
			$this->get_field_name( 'title' ),
			isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : ''
		);
		/**
		 * type
		 */
		$current = '400';
		if ( isset( $instance['type'] ) && ! empty( $instance['type'] ) ) {
			$current = $instance['type'];
		}
		printf(
			'<p><label for="%s">%s <input type="number" id="%s" name="%s" value="%d" /></label></p>',
			$this->get_field_id( 'type' ),
			__( 'Map width', 'interactive_polish_map' ),
			$this->get_field_id( 'type' ),
			$this->get_field_name( 'type' ),
			$current
		);
		/**
		 * menu
		 */
		$current = 'standard';
		if ( isset( $instance['menu'] ) && ! empty( $instance['menu'] ) ) {
			$current = $instance['menu'];
		}
		$current = apply_filters( 'iworks_interactive_polish_map_menu_legacy', $current );

		$select = '';
		foreach ( $this->options->get_values( 'menu' )  as $value => $data ) {
			$select .= sprintf(
				'<option value="%s"%s>%s</option>',
				$value,
				( $value == $current ) ? ' selected="selected"' : '',
				$data['label']
			);
		}
		printf(
			'<p><label for="%s">%s <select id="%s" name="%s">%s</select></label></p>',
			$this->get_field_id( 'menu' ),
			__( 'Display list', 'interactive_polish_map' ),
			$this->get_field_id( 'menu' ),
			$this->get_field_name( 'menu' ),
			$select
		);
	}

}

