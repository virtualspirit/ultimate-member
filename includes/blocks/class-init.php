<?php
namespace um\blocks;


if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class Init
 *
 * @package um\blocks
 */
class Init {


	/**
	 * Init constructor.
	 */
	function __construct() {
		$this->js_url = um_url . 'includes/blocks/assets/js/';

		add_filter( 'block_categories', [ &$this, 'register_blocks_category' ], 9999999, 2 );

		add_action( 'enqueue_block_assets', array( &$this, 'enqueue_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( &$this, 'enqueue_assets' ) );

		add_action( 'init',  [ &$this, 'register_blocks' ] );
	}


	/**
	 * Add Gutenberg category `Ultimate Member`
	 *
	 * @param array $categories
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	function register_blocks_category( $categories, $post ) {
		$categories = array_merge(
			$categories,
			[
				[
					'slug'  => 'ultimate-member',
					'title' => __( 'Ultimate Member', 'ultimate-member' ),
				],
			]
		);

		return $categories;
	}


	function enqueue_assets() {
		wp_register_script('um-block-index', $this->js_url . 'index.js', [ 'wp-blocks', 'wp-components', 'wp-hooks', 'wp-i18n' ], ultimatemember_version, true );
		wp_register_script('um-block-form', $this->js_url . 'form/editor.js', [ 'um-block-index' ], ultimatemember_version, true );
		wp_register_script('um-block-login-form', $this->js_url . 'login-form/editor.js', [ 'um-block-index' ], ultimatemember_version, true );
	}



	/**
	 * @param array $attributes The block attributes.
	 *
	 * @return string
	 */
	function render_block_form( $attributes ) {
		return '';
//		static $seen_refs = array();
//
//		if ( empty( $attributes['ref'] ) ) {
//			return '';
//		}
//
//		$reusable_block = get_post( $attributes['ref'] );
//		if ( ! $reusable_block || 'wp_block' !== $reusable_block->post_type ) {
//			return '';
//		}
//
//		if ( isset( $seen_refs[ $attributes['ref'] ] ) ) {
//			if ( ! is_admin() ) {
//				trigger_error(
//					sprintf(
//					// translators: %s is the user-provided title of the reusable block.
//						__( 'Could not render Reusable Block <strong>%s</strong>: blocks cannot be rendered inside themselves.' ),
//						$reusable_block->post_title
//					),
//					E_USER_WARNING
//				);
//			}
//
//			// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
//			// is set in `wp_debug_mode()`.
//			$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
//			            defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
//
//			return $is_debug ?
//				// translators: Visible only in the front end, this warning takes the place of a faulty block.
//				__( '[block rendering halted]' ) :
//				'';
//		}
//
//		if ( 'publish' !== $reusable_block->post_status || ! empty( $reusable_block->post_password ) ) {
//			return '';
//		}
//
//		$seen_refs[ $attributes['ref'] ] = true;
//
//		$result = do_blocks( $reusable_block->post_content );
//		unset( $seen_refs[ $attributes['ref'] ] );
//		return $result;
	}


	/**
	 *
	 */
	function register_blocks() {
		register_block_type_from_metadata(
			__DIR__ . '/form',
			[
				//'render_callback'   => [ &$this, 'render_block_' . $block_name ],
			]
		);

		register_block_type_from_metadata(
			__DIR__ . '/login-form',
			[
				//'render_callback'   => [ &$this, 'render_block_' . $block_name ],
			]
		);
	}
}