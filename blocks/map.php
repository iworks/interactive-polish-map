<?php
/**
 * Server-side rendering of the `ipm/map` block.
 *
 * @package WordPress
 */

/**
 * Renders the `ipm/map` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the map list/dropdown markup.
 */
function render_block_ipm_map( $attributes ) {
	static $block_id = 0;
	$block_id++;


    $attributes = wp_parse_args(
        $attributes,
        array(
            'style' => 400,
            'menu' => 'hide',
        )
    );


	$args = array(
		'echo'         => false,
		'hierarchical' => ! empty( $attributes['showHierarchy'] ),
		'orderby'      => 'name',
		'show_count'   => ! empty( $attributes['showPostCounts'] ),
		'title_li'     => '',
	);

	if ( ! empty( $attributes['displayAsDropdown'] ) ) {
		$id                       = 'wp-block-map-' . $block_id;
		$args['id']               = $id;
		$args['show_option_none'] = __( 'Select Category' );
		$wrapper_markup           = '<div %1$s><label class="screen-reader-text" for="' . $id . '">' . __( 'map' ) . '</label>%2$s</div>';
		$items_markup             = wp_dropdown_map( $args );

		if ( ! is_admin() ) {
			// Inject the dropdown script immediately after the select dropdown.
			$items_markup = preg_replace(
				'#(?<=</select>)#',
				build_dropdown_script_block_ipm_map( $id ),
				$items_markup,
				1
			);
		}
	} else {
		$wrapper_markup = '<div %1$s>%2$s</div>';
        $items_markup   = do_shortcode(
            sprintf(
                '[interactive-polish-map style="%d" menu="%s"]',
                $attributes['style'],
                $attributes['menu']
            )
        );
    }
	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		$wrapper_markup,
		$wrapper_attributes,
		$items_markup
	);
}

/**
 * Generates the inline script for a map dropdown field.
 *
 * @param string $dropdown_id ID of the dropdown field.
 *
 * @return string Returns the dropdown onChange redirection script.
 */
function build_dropdown_script_block_ipm_map( $dropdown_id ) {
	ob_start();
	?>
	<script type='text/javascript'>
	/* <![CDATA[ */
	( function() {
		var dropdown = document.getElementById( '<?php echo esc_js( $dropdown_id ); ?>' );
		function onCatChange() {
			if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
				location.href = "<?php echo home_url(); ?>/?cat=" + dropdown.options[ dropdown.selectedIndex ].value;
			}
		}
		dropdown.onchange = onCatChange;
	})();
	/* ]]> */
	</script>
	<?php
	return ob_get_clean();
}

/**
 * Registers the `ipm/map` block on server.
 */
function register_block_ipm_map() {
	$result = register_block_type_from_metadata(
		__DIR__ . '/map',
		array(
			'render_callback' => 'render_block_ipm_map',
		)
    );
}
add_action( 'init', 'register_block_ipm_map' );
