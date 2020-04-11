jQuery(document).ready(function ($) {

	/**
	 * This code is not used if $wp_version >= '5.4'
	 */
	if ( typeof (um_menu_restriction_data.menu_data) === 'object' ) {
		var template = wp.template('um-nav-menus-fields');

		/**
		 * Add block "Ultimate Member Menu Settings" to a new menu item
		 */
		$(document).on('menu-item-added', function (e, $menuMarkup) {
			var id = $($menuMarkup).attr('id').substr(10);

			var template_content = template({
				menu_item_id: id,
				restriction_data: {
					um_nav_columns: 2,
					um_nav_public: 0,
					um_nav_roles: [],
					um_nav_roles_all: um_menu_restriction_data.roles_all
				}
			});

			if ( $($menuMarkup).find('fieldset.field-move').length > 0 ) {
				$($menuMarkup).find('fieldset.field-move').before(template_content);
			} else {
				$($menuMarkup).find('.menu-item-actions').before(template_content);
			}
		});


		/**
		 * Add block "Ultimate Member Menu Settings" to existed menu items
		 */
		$('ul#menu-to-edit > li').each(function () {
			var id = $(this).attr('id').substr(10);
			var template_content = template({
				menu_item_id: id,
				restriction_data: um_menu_restriction_data.menu_data[ id ]
			});

			if ( $(this).find('fieldset.field-move').length > 0 ) {
				$(this).find('fieldset.field-move').before(template_content);
			} else {
				$(this).find('.menu-item-actions').before(template_content);
			}
		});
	}


	/**
	 * Update restriction data for the menu item on the page [Appearence > Customize > Menus]
	 * @since 2.1.6
	 */
	jQuery('#customize-theme-controls').on('change', 'div.um-nav-edit', function (e) {
		var $parent = jQuery(e.currentTarget);
		var $target = jQuery(e.target);

		/* Toggle roles block */
		if ( $target.is('select[name*=um_nav_public]') ) {
			if ( $target.val() == 2 ) {
				$parent.find('p.um-nav-roles').show();
			} else {
				$parent.find('p.um-nav-roles').hide();
			}
		}

		/* Prepare request */
		var data = $parent.find("input, textarea, select").serializeArray();
		data.push({
			name: 'action',
			value: 'um-customize-update_nav_menu_item'
		});
		data.push({
			name: 'menu_item_id',
			value: $parent.data('menu_item_id')
		});

		/* Process AJAX request to update menu item restriction */
		wp.ajax.post('um-customize-update_nav_menu_item', data).fail(function (response) {
			if ( typeof (response) === 'string' ) {
				console.log(response);
			}
		});
	});
});