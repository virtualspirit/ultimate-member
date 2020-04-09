jQuery(document).ready(function ($) {
	var template = wp.template( 'um-nav-menus-fields' );

	$( document ).on( 'menu-item-added', function ( e, $menuMarkup ) {
		var id = $( $menuMarkup ).attr('id').substr(10);

		var template_content = template({
			menu_item_id: id,
			restriction_data:{
				um_nav_columns:2,
				um_nav_public:0,
				um_nav_roles:[],
				um_nav_roles_all:um_menu_restriction_data.roles_all
			}
		});

		if ( $( $menuMarkup ).find( 'fieldset.field-move' ).length > 0 ) {
			$( $menuMarkup ).find( 'fieldset.field-move' ).before( template_content );
		} else {
			$( $menuMarkup ).find( '.menu-item-actions' ).before( template_content );
		}
	});


	/**
	 * The variable um_menu_restriction_data.menu_data appears if $wp_version < '5.4'
	 */
	if( typeof( um_menu_restriction_data.menu_data ) === 'object' ){
		$( 'ul#menu-to-edit > li' ).each( function(){
			var id = $(this).attr('id').substr(10);
			var template_content = template({
				menu_item_id: id,
				restriction_data: um_menu_restriction_data.menu_data[ id ]
			});

			if ( $( this ).find( 'fieldset.field-move' ).length > 0 ) {
				$( this ).find( 'fieldset.field-move' ).before( template_content );
			} else {
				$( this ).find( '.menu-item-actions' ).before( template_content );
			}
		});
	}
});