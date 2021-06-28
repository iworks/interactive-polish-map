<?php

function iworks_interactive_polish_map_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'use_tabs'        => false,
		'version'         => '0.0',
		'page_title'      => __( 'Interactive Polish Map', 'interactive-polish-map' ),
		'menu_title'      => __( 'Polish Map', 'interactive-polish-map' ),
		'menu'            => 'theme',
		'enqueue_scripts' => array(
			'interactive_polish_map-admin-js',
		),
		'enqueue_styles'  => array(
			'interactive_polish_map-admin',
			'interactive-polish-map',
		),
		'options'         => array(
			array(
				'name'              => 'size',
				'type'              => 'number',
				'class'             => 'small-text slider',
				'th'                => __( 'Map Size', 'interactive-polish-map' ),
				'label'             => __( 'px', 'interactive-polish-map' ),
				'default'           => 500,
				'min'               => 120,
				'max'               => 1200,
				'sanitize_callback' => 'absint',
			),
			array(
				'name'              => 'menu',
				'type'              => 'radio',
				'th'                => __( 'Districts List Position', 'interactive-polish-map' ),
				'default'           => 'under',
				'radio'             => array(
					'hide'                 => array( 'label' => __( 'Hidden', 'interactive-polish-map' ) ),
					'left'                 => array( 'label' => __( 'On left', 'interactive-polish-map' ) ),
					'right'                => array( 'label' => __( 'On right', 'interactive-polish-map' ) ),
					'before'               => array( 'label' => __( 'Before', 'interactive-polish-map' ) ),
					'after'                => array( 'label' => __( 'After', 'interactive-polish-map' ) ),
					'after-two-columns'    => array( 'label' => __( 'After', 'interactive-polish-map' ) ),
					'bottom-three-columns' => array( 'label' => __( 'After', 'interactive-polish-map' ) ),
				),
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'style',
				'type'              => 'radio',
				'th'                => __( 'Color Style', 'interactive-polish-map' ),
				'default'           => 'default',
				'radio'             => array(
					'default' => array( 'label' => __( 'Default', 'interactive-polish-map' ) ),
					'own'     => array( 'label' => __( 'My own', 'interactive-polish-map' ) ),
					'auto'    => array( 'label' => __( 'Auto Adjust', 'interactive-polish-map' ) ),
				),
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'color',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => __( 'Foreground color', 'interactive-polish-map' ),
				'sanitize_callback' => 'esc_html',
				'default'           => '#4d80b3',
				'use_name_as_id'    => true,
			),
			array(
				'name'              => 'border',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => __( 'Border Color', 'interactive-polish-map' ),
				'sanitize_callback' => 'esc_html',
				'default'           => '#666',
				'use_name_as_id'    => true,
			),
		),
		'metaboxes'       => array(
			'assistance' => array(
				'title'    => __( 'We are waiting for your message', 'interactive-polish-map' ),
				'callback' => 'iworks_interactive_polish_map_options_need_assistance',
				'context'  => 'side',
				'priority' => 'core',
			),
			'love'       => array(
				'title'    => __( 'I love what I do!', 'interactive-polish-map' ),
				'callback' => 'iworks_interactive_polish_map_options_loved_this_plugin',
				'context'  => 'side',
				'priority' => 'core',
			),
		),
		'provinces'       => array(
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
	);
	return $options;
}

function iworks_interactive_polish_map_options_need_assistance( $iworks_interactive_polish_map ) {
	$content = apply_filters( 'iworks_rate_assistance', '', 'interactive-polish-map' );
	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}

	?>
<p><?php _e( 'We are waiting for your message', 'interactive-polish-map' ); ?></p>
<ul>
	<li><a href="<?php _ex( 'https://wordpress.org/support/plugin/interactive-polish-map/', 'link to support forum on WordPress.org', 'interactive-polish-map' ); ?>"><?php _e( 'WordPress Help Forum', 'interactive-polish-map' ); ?></a></li>
</ul>
	<?php
}

function iworks_interactive_polish_map_options_loved_this_plugin( $iworks_interactive_polish_map ) {
	$content = apply_filters( 'iworks_rate_love', '', 'interactive-polish-map' );
	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}
	?>
<p><?php _e( 'Below are some links to help spread this plugin to other users', 'interactive-polish-map' ); ?></p>
<ul>
	<li><a href="https://wordpress.org/support/plugin/interactive-polish-map/reviews/#new-post"><?php _e( 'Give it a five stars on WordPress.org', 'interactive-polish-map' ); ?></a></li>
	<li><a href="<?php _ex( 'https://wordpress.org/plugins/interactive-polish-map/', 'plugin home page on WordPress.org', 'interactive-polish-map' ); ?>"><?php _e( 'Link to it so others can easily find it', 'interactive-polish-map' ); ?></a></li>
</ul>
	<?php
}
