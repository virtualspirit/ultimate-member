<?php

namespace um\admin\core;

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'um\admin\core\Admin_Navmenu' ) ) {


	/**
	 * Class Admin_Navmenu
	 * @package um\admin\core
	 */
	class Admin_Navmenu {

		/**
		 * @var array
		 */
		protected static $fields = array();

		/**
		 * The restriction data for menu items
		 * @var array
		 */
		protected static $restriction_data = array();

		/**
		 * The list of existing roles
		 * @var array
		 */
		protected static $roles = array();

		function __construct() {
			global $wp_version;

			self::$fields = array(
				'um_nav_public'	 => __( 'Display Mode', 'ultimate-member' ),
				'um_nav_roles'	 => __( 'By Role', 'ultimate-member' )
			);

			add_action( 'wp_update_nav_menu_item', array( &$this, '_save' ), 10, 3 );
			//add_filter( 'manage_nav-menus_columns', array( &$this, '_columns' ), 99 );

			add_action( 'load-nav-menus.php', array( &$this, 'enqueue_nav_menus_scripts' ) );
			add_action( 'admin_footer-nav-menus.php', array( &$this, '_wp_template' ) );

			/**
			 * New hooks let you add custom fields to menu items
			 * @link https://make.wordpress.org/core/2020/02/25/wordpress-5-4-introduces-new-hooks-to-add-custom-fields-to-menu-items/
			 */
			if ( $wp_version >= '5.4' ) {
				add_action( 'customize_controls_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
				add_action( 'customize_register', array( $this, 'customize_register' ), 90 );
				add_action( 'customize_save', array( $this, 'customize_save' ), 90 );
				add_action( 'wp_ajax_um-customize-update_nav_menu_item', array( $this, 'customize_update_nav_menu_item' ) );
				add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'wp_nav_menu_item_custom_fields' ), 20, 5 );
				add_action( 'wp_nav_menu_item_custom_fields_customize_template', array( $this, 'wp_nav_menu_item_custom_fields_customize_template' ), 20 );
			}
		}

		/**
		 * @param $columns
		 *
		 * @return array
		 */
		function _columns( $columns ) {
			$columns = array_merge( $columns, self::$fields );

			return $columns;
		}

		/**
		 * Fires after a navigation menu item has been updated.
		 *
		 * @see wp_update_nav_menu_item()
		 *
		 * @param int   $menu_id         ID of the updated menu.
		 * @param int   $menu_item_db_id ID of the updated menu item.
		 * @param array $args            An array of arguments used to update a menu item.
		 */
		function _save( $menu_id, $menu_item_db_id, $args ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}

			if ( empty( $_POST['menu-item-db-id'] ) || !in_array( $menu_item_db_id, $_POST['menu-item-db-id'] ) ) {
				return;
			}

			foreach ( self::$fields as $_key => $label ) {

				$key = sprintf( 'menu-item-%s', $_key );

				// Sanitize
				if ( !empty( $_POST[$key][$menu_item_db_id] ) ) {
					// Do some checks here...
					$value = is_array( $_POST[$key][$menu_item_db_id] ) ?
						array_keys( $_POST[$key][$menu_item_db_id] ) : $_POST[$key][$menu_item_db_id];
				} else {
					$value = null;
				}

				// Update
				if ( !is_null( $value ) ) {
					update_post_meta( $menu_item_db_id, $key, $value );
				} else {
					delete_post_meta( $menu_item_db_id, $key );
				}
			}
		}

		/**
		 * The template for a new menu items.
		 */
		function _wp_template() {
			?>
			<script type="text/html" id="tmpl-um-nav-menus-fields">
				<?php $this->wp_nav_menu_item_custom_fields_customize_template(); ?>
			</script>
			<?php
		}

		/**
		 *
		 */
		function admin_enqueue_scripts() {
			global $wp_version;

			UM()->admin_enqueue()->load_nav_manus_scripts();

			$menu_restriction_data = array(
				'roles_all'  => UM()->roles()->get_roles( false, array( 'administrator' ) ),
				'wp_version' => $wp_version
			);

			if ( $wp_version < '5.4' ) {
				$menu_restriction_data['menu_data'] = array();
				$menus = get_posts( 'post_type=nav_menu_item&numberposts=-1' );
				foreach ( $menus as $data ) {
					$item_id = $data->ID;
					$menu_restriction_data['menu_data'][$data->ID] = $this->get_restriction_data( $item_id );
				}
			}

			wp_localize_script( 'um_admin_nav_manus', 'um_menu_restriction_data', $menu_restriction_data );
		}

		/**
		 * Put restriction data for the menu item to the customize controls json
		 *
		 * @since  2.1.6
		 * @hook   customize_register
		 * @see    https://codex.wordpress.org/Plugin_API/Action_Reference/customize_register
		 *
		 * @param  WP_Customize_Manager  $wp_customize
		 */
		public function customize_register( $wp_customize ) {
			$controls = $wp_customize->controls();
			foreach ( $controls as &$control ) {
				if ( is_object( $control ) && is_a( $control, 'WP_Customize_Nav_Menu_Item_Control' ) ) {
					$item_id = $control->setting->post_id;
					$control->json['restriction_data'] = $this->get_restriction_data( $item_id );
				}
			}
		}

		/**
		 * AJAX handler: Save restriction data for the menu item on the page [Appearence > Customize > Menus ]
		 *
		 * @since  2.1.6
		 * @hook   wp_ajax_um-customize-update_nav_menu_item
		 */
		public function customize_update_nav_menu_item() {

			$menu_item_id = filter_input( INPUT_POST, 'menu_item_id', FILTER_SANITIZE_NUMBER_INT );
			if ( empty( $menu_item_id ) ) {
				wp_send_json_error( __( 'UM Warning: The menu_item_id is required', 'ultimate-member' ) );
			}

			foreach ( self::$fields as $_key => $label ) {
				$key = sprintf( 'menu-item-%s', $_key );

				// Sanitize
				$value = null;
				if ( isset( $_POST[$key] ) && isset( $_POST[$key][$menu_item_id] ) ) {
					$value = is_array( $_POST[$key][$menu_item_id] ) ? array_keys( $_POST[$key][$menu_item_id] ) : $_POST[$key][$menu_item_id];
				}

				// Update
				if ( !is_null( $value ) ) {
					update_post_meta( $menu_item_id, $key, $value );
				}
			}

			wp_send_json_success( sprintf( __( 'The menu item %1$s is updated', 'ultimate-member' ), $menu_item_id ) );
		}

		/**
		 *
		 */
		public function enqueue_nav_menus_scripts() {
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Get restriction data for the menu item
		 *
		 * @since  2.1.6
		 *
		 * @param  integer  $item_id
		 * @return type
		 */
		public function get_restriction_data( $item_id ) {
			if ( empty( self::$roles ) ) {
				self::$roles = UM()->roles()->get_roles( false, array( 'administrator' ) );
			}

			if ( empty( self::$restriction_data[$item_id] ) ) {
				$um_nav_public = get_post_meta( $item_id, 'menu-item-um_nav_public', true );
				$um_nav_roles_meta = (array) get_post_meta( $item_id, 'menu-item-um_nav_roles', true );

				$um_nav_roles = array();
				foreach ( $um_nav_roles_meta as $key => $value ) {
					if ( is_int( $key ) ) {
						$um_nav_roles[] = $value;
					}
				}

				self::$restriction_data[$item_id] = array(
					'um_nav_columns'   => apply_filters( 'wp_nav_menu_item:um_nav_columns', 2, $item_id ),
					'um_nav_public'		 => $um_nav_public,
					'um_nav_roles'		 => $um_nav_roles,
					'um_nav_roles_all' => self::$roles
				);
			}

			return self::$restriction_data[$item_id];
		}

		/**
		 * Adds block "Ultimate Member Menu Settings" to the [Appearance > Menus]
		 *
		 * @since  2.1.6
		 * @hook   wp_nav_menu_item_custom_fields
		 *
		 * @param  int       $item_id Menu item ID.
		 * @param  WP_Post   $item    Menu item data object.
		 * @param  int       $depth   Depth of menu item. Used for padding.
		 * @param  stdClass  $args    An object of menu item arguments.
		 * @param  int       $id      Nav menu ID.
		 */
		public function wp_nav_menu_item_custom_fields( $item_id, $item, $depth, $args, $id ) {

			$restriction_data = $this->get_restriction_data( $item_id );

			extract( array_merge( array(
				'um_nav_public'		 => 0,
				'um_nav_roles'		 => array(),
				'um_nav_roles_all' => self::$roles,
				'um_nav_columns'   => 2
					), $restriction_data ) );
			?>
			<div class="um-nav-edit" data-menu_item_id="<?php echo esc_attr( $item_id ); ?>">
				<div class="clear"></div>
				<h4 style="margin-bottom: 0.6em;"><?php _e( "Ultimate Member Menu Settings", 'ultimate-member' ) ?></h4>

				<p class="description description-wide um-nav-mode">
				<label for="edit-menu-item-um_nav_public-<?php echo esc_attr( $item_id ); ?>">
					<?php _e( "Who can see this menu link?", 'ultimate-member' ); ?><br/>
					<select id="edit-menu-item-um_nav_public-<?php echo esc_attr( $item_id ); ?>" name="menu-item-um_nav_public[<?php echo esc_attr( $item_id ); ?>]" style="width:100%;">
						<option value="0" <?php selected( $um_nav_public, 0 ); ?>><?php _e( 'Everyone', 'ultimate-member' ) ?></option>
						<option value="1" <?php selected( $um_nav_public, 1 ); ?>><?php _e( 'Logged Out Users', 'ultimate-member' ) ?></option>
						<option value="2" <?php selected( $um_nav_public, 2 ); ?>><?php _e( 'Logged In Users', 'ultimate-member' ) ?></option>
					</select>
				</label>
			</p>

			<p class="description description-wide um-nav-roles" <?php echo $um_nav_public == 2 ? 'style="display: block;"' : ''; ?>><?php _e( "Select the member roles that can see this link", 'ultimate-member' ) ?><br>

				<?php
				$i = 0;
				$html = '';
				$per_page = ceil( count( $um_nav_roles_all ) / $um_nav_columns );
				while ( $i < $um_nav_columns ) {
					$section_fields_per_page = array_slice( $um_nav_roles_all, $i * $per_page, $per_page );
					$html .= '<span class="um-form-fields-section" style="width:' . floor( 100 / $um_nav_columns ) . '% !important;">';

					foreach ( $section_fields_per_page as $k => $title ) {
						$id_attr = ' id="edit-menu-item-um_nav_roles-' . $item_id . '_' . $k . '" ';
						$for_attr = ' for="edit-menu-item-um_nav_roles-' . $item_id . '_' . $k . '" ';
						$checked_attr = checked( in_array( $k, $um_nav_roles ), true, false );
						$html .= "<label {$for_attr}> <input type='checkbox' {$id_attr} name='menu-item-um_nav_roles[{$item_id}][{$k}]' value='1' {$checked_attr} /> <span>{$title}</span> </label>";
					}

					$html .= '</span>';
					$i++;
				}
				echo $html;
				?>
			</p>
			<div class="clear"></div>
			</div>
			<?php
		}

		/**
		 * Adds block "Ultimate Member Menu Settings" to the [Appearance > Customize > Menus]
		 *
		 * @since  2.1.6
		 * @hook   wp_nav_menu_item_custom_fields_customize_template
		 */
		public function wp_nav_menu_item_custom_fields_customize_template() {
			?>
			<# if( typeof( data.restriction_data ) === 'object' ) { #>
			<div class="um-nav-edit" data-menu_item_id="{{ data.menu_item_id }}">
				<div class="clear"></div>
				<h4 style="margin-bottom: 0.6em;"><?php _e( "Ultimate Member Menu Settings", 'ultimate-member' ) ?></h4>

				<p class="description description-wide um-nav-mode" >
					<label for="edit-menu-item-um_nav_public-{{ data.menu_item_id }}">
						<?php _e( "Who can see this menu link?", 'ultimate-member' ); ?><br/>
						<select id="edit-menu-item-um_nav_public-{{ data.menu_item_id }}" name="menu-item-um_nav_public[{{ data.menu_item_id }}]" style="width:100%;">
							<option value="0" <# if( data.restriction_data.um_nav_public == '0' ){ #>selected="selected"<# } #>><?php _e( 'Everyone', 'ultimate-member' ) ?></option>
							<option value="1" <# if( data.restriction_data.um_nav_public == '1' ){ #>selected="selected"<# } #>><?php _e( 'Logged Out Users', 'ultimate-member' ) ?></option>
							<option value="2" <# if( data.restriction_data.um_nav_public == '2' ){ #>selected="selected"<# } #>><?php _e( 'Logged In Users', 'ultimate-member' ) ?></option>
						</select>
					</label>
				</p>

				<p class="description description-wide um-nav-roles" style="<# if( data.restriction_data.um_nav_public == '2' ){ #>display: block;<# } #>width:100%;"><?php _e( "Select the member roles that can see this link", 'ultimate-member' ) ?><br/>

					<# var colWidth = Math.floor( 100 / data.restriction_data.um_nav_columns ); #>
					<# for( var i in data.restriction_data.um_nav_roles_all ) { #>
						<label style="float:left; width:{{ colWidth }}%;" id="edit-menu-item-um_nav_roles-{{ data.menu_item_id }}_{{ i }}"><input for="edit-menu-item-um_nav_roles-{{ data.menu_item_id }}_{{ i }}" type="checkbox" name="menu-item-um_nav_roles[{{ data.menu_item_id }}][{{ i }}]" value="1" <# if( _.contains( data.restriction_data.um_nav_roles, i ) ){ #>checked="checked"<# } #> /> <span>{{ data.restriction_data.um_nav_roles_all[i] }}</span></label>
					<# } #>

				</p>
			</div>
			<# } #>
			<?php
		}

	}

}