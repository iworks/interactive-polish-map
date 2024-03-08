<?php
/*
Copyright 2021-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

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

if ( class_exists( 'iworks_posttype_map' ) ) {
	return;
}

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-posttypes.php';

class iworks_posttype_map extends iworks_posttypes {

	protected $post_type_name = 'iworks_map';

	protected $countries = array();

	public function __construct( $options ) {
		parent::__construct( $options );
		$this->fields                                 = array(
			'event_data' => array(
				'date_start' => array(
					'type'  => 'date',
					'label' => __( 'Date start', 'interactive-polish-map' ),
				),
				'date_end'   => array(
					'type'  => 'date',
					'label' => __( 'Date start', 'interactive-polish-map' ),
				),
			),
		);
		$this->post_type_objects[ $this->get_name() ] = $this;
		/**
		 * change default columns
		 */
		add_filter( "manage_{$this->get_name()}_posts_columns", array( $this, 'add_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
	}

	public function register() {
		$labels                               = array(
			'name'                  => _x( 'Maps', 'Map General Name', 'interactive-polish-map' ),
			'singular_name'         => _x( 'Map', 'Map Singular Name', 'interactive-polish-map' ),
			'menu_name'             => __( 'Maps', 'interactive-polish-map' ),
			'name_admin_bar'        => __( 'Map', 'interactive-polish-map' ),
			'archives'              => __( 'Map Archives', 'interactive-polish-map' ),
			'attributes'            => __( 'Map Attributes', 'interactive-polish-map' ),
			'parent_item_colon'     => __( 'Parent Map:', 'interactive-polish-map' ),
			'all_items'             => __( 'Maps', 'interactive-polish-map' ),
			'add_new_item'          => __( 'Add New Map', 'interactive-polish-map' ),
			'add_new'               => __( 'Add New', 'interactive-polish-map' ),
			'new_item'              => __( 'New Map', 'interactive-polish-map' ),
			'edit_item'             => __( 'Edit Map', 'interactive-polish-map' ),
			'update_item'           => __( 'Update Map', 'interactive-polish-map' ),
			'view_item'             => __( 'View Map', 'interactive-polish-map' ),
			'view_items'            => __( 'View Maps', 'interactive-polish-map' ),
			'search_items'          => __( 'Search Map', 'interactive-polish-map' ),
			'not_found'             => __( 'Not found', 'interactive-polish-map' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'interactive-polish-map' ),
			'featured_image'        => __( 'Featured Image', 'interactive-polish-map' ),
			'set_featured_image'    => __( 'Set featured image', 'interactive-polish-map' ),
			'remove_featured_image' => __( 'Remove featured image', 'interactive-polish-map' ),
			'use_featured_image'    => __( 'Use as featured image', 'interactive-polish-map' ),
			'insert_into_item'      => __( 'Insert into contractor', 'interactive-polish-map' ),
			'uploaded_to_this_item' => __( 'Uploaded to this contractor', 'interactive-polish-map' ),
			'items_list'            => __( 'Maps list', 'interactive-polish-map' ),
			'items_list_navigation' => __( 'Maps list navigation', 'interactive-polish-map' ),
			'filter_items_list'     => __( 'Filter contractors list', 'interactive-polish-map' ),
		);
		$args                                 = array(
			'label'                => __( 'Map', 'interactive-polish-map' ),
			'description'          => __( 'Map Description', 'interactive-polish-map' ),
			'labels'               => $labels,
			'supports'             => array( 'title', 'thumbnail', 'editor' ),
			'taxonomies'           => array(),
			'hierarchical'         => true,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => 'edit.php',
			'show_in_admin_bar'    => false,
			'show_in_nav_menus'    => false,
			'show_in_rest'         => true,
			'can_export'           => true,
			'has_archive'          => true,
			'exclude_from_search'  => true,
			'publicly_queryable'   => false,
			'capability_type'      => 'page',
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
		);
		$this->types[ $this->post_type_name ] = register_post_type( $this->post_type_name, $args );
	}

	public function register_meta_boxes( $post ) {
		add_meta_box( 'event-data', __( 'Map Data', 'interactive-polish-map' ), array( $this, 'event_data' ), $this->post_type_name );
	}

	public function event_data( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}


	public function save_post_meta( $post_id, $post, $update ) {
		$this->save_post_meta_fields( $post_id, $post, $update, $this->fields );
	}


	/**
	 * Get custom column values.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $column Column name,
	 * @param integer $post_id Current post id (contractor),
	 */
	public function custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'date_start':
				echo get_post_meta( $post_id, $this->options->get_option_name( 'event_data_date_start' ), true );
				break;
			case 'date_end':
				echo get_post_meta( $post_id, $this->options->get_option_name( 'event_data_date_end' ), true );
				break;
		}
	}

	/**
	 * change default columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns list of columns.
	 * @return array $columns list of columns.
	 */
	public function add_columns( $columns ) {
		unset( $columns['date'] );
		$columns['date_start'] = __( 'Date Start', 'interactive-polish-map' );
		$columns['date_end']   = __( 'Date End', 'interactive-polish-map' );
		return $columns;
	}

}

