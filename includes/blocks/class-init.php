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

		add_action( 'init',  [ &$this, 'register_blocks' ] );
		add_filter( 'block_categories', [ &$this, 'register_blocks_category' ], 10, 2 );

		add_action( 'enqueue_block_assets', array( &$this, 'enqueue_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( &$this, 'enqueue_assets' ) );
	}


	/**
	 *
	 */
//	function register_blocks() {
//		/**
//		 * create gutenberg blocks
//		 */
//		register_block_type( 'um-block/um-forms', array(
//			'editor_script' => 'um-blocks-shortcode-js',
//		) );
//
//		register_block_type( 'um-block/um-member-directories', array(
//			'editor_script' => 'um-blocks-shortcode-js',
//		) );
//
//		register_block_type( 'um-block/um-password-reset', array(
//			'editor_script' => 'um-blocks-shortcode-js',
//		) );
//
//		register_block_type( 'um-block/um-account', array(
//			'editor_script' => 'um-blocks-shortcode-js',
//		) );
//	}


	/**
	 *
	 */
	function block_assets() {
		$this->restriction_blocks_js();
		$this->load_gutenberg_shortcode_blocks();
	}


	/**
	 * Load Gutenberg scripts
	 */
	function restriction_blocks_js() {
		$restricted_blocks = UM()->options()->get( 'restricted_blocks' );
		if ( empty( $restricted_blocks ) ) {
			return;
		}

		wp_register_script( 'um-restriction-blocks', $this->js_url . 'restriction-blocks.js',
			[ 'wp-blocks', 'wp-components', 'wp-hooks', 'wp-i18n' ], ultimatemember_version, true );
		//wp_set_script_translations( 'um_block_js', 'ultimate-member' );

		$restrict_options = [];
		$roles = UM()->roles()->get_roles( false );
		if ( ! empty( $roles ) ) {
			foreach ( $roles as $role_key => $title ) {
				$restrict_options[] = [
					'label' => $title,
					'value' => $role_key,
				];
			}
		}
		wp_localize_script( 'um-restriction-blocks', 'um_restrict_roles', $restrict_options );
		wp_enqueue_script( 'um-restriction-blocks' );
	}


	/**
	 * Load Gutenberg blocks js
	 */
	function load_gutenberg_shortcode_blocks() {
		wp_register_script( 'um-blocks-shortcode-js', $this->js_url . 'um-admin-blocks-shortcode.js', array( 'wp-i18n', 'wp-blocks', 'wp-components', /*'rich-text'*/ ), ultimatemember_version, true );
		wp_set_script_translations( 'um-blocks-shortcode-js', 'ultimate-member' );
		wp_enqueue_script( 'um-blocks-shortcode-js' );

		$account_settings = array(
			'password'      => array(
				'label'     => __( 'Password', 'ultimate-member' ),
				'enabled'   => UM()->options()->get( 'account_tab_password' ),
			),
			'privacy'       => array(
				'label'     => __( 'Privacy', 'ultimate-member' ),
				'enabled'   => UM()->options()->get( 'account_tab_privacy' ),
			),
			'notifications' => array(
				'label'     => __( 'Notifications', 'ultimate-member' ),
				'enabled'   => UM()->options()->get( 'account_tab_notifications' ),
			),
			'delete'        => array(
				'label'     => __( 'Delete', 'ultimate-member' ),
				'enabled'   => UM()->options()->get( 'account_tab_delete' ),
			),
		);
		wp_localize_script( 'um-blocks-shortcode-js', 'um_account_settings', $account_settings );

		do_action( 'um_load_gutenberg_js' );
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
		$categories[] = [
			'slug'  => 'ultimate-member',
			'title' => __( 'Ultimate Member', 'ultimate-member' ),
		];

		return $categories;
	}


	/**
	 * @param array $attributes The block attributes.
	 *
	 * @return string
	 */
	function render_block_form( $attributes ) {
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
				'render_callback' => [ &$this, 'render_block_form' ],
			]
		);
	}
}