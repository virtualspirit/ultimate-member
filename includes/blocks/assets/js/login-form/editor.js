registerBlockType( 'ultimate-member/login-form', {
	title: 'Ultimate Member: Login Form',
	category: 'ultimate-member',
	icon: 'smiley',
	description: 'Learning in progress',
	keywords: ['example', 'test'],
	edit: ( props ) => {
		const { attributes, setAttributes } = props;

		return [
			wp.element.createElement(
				wp.blockEditor.InspectorControls,
				{},
				wp.element.createElement(
					wp.components.PanelBody,
					{},
					wp.element.createElement(
						wp.components.SelectControl,
						{
							label: wp.i18n.__( 'Redirection after Login', 'ultimate-member' ),
							value: props.attributes.redirectType,
							options: [
								{label: wp.i18n.__( 'Default', 'ultimate-member' ), value: ''},
								{label: wp.i18n.__( 'Redirect to profile', 'ultimate-member' ), value: 'redirect_profile'},
								{label: wp.i18n.__( 'Redirect to URL', 'ultimate-member' ), value: 'redirect_url'},
								{label: wp.i18n.__( 'Refresh active page', 'ultimate-member' ), value: 'refresh'},
								{label: wp.i18n.__( 'Redirect to WordPress Admin', 'ultimate-member' ), value: 'redirect_admin'},
							],
							onChange: function onChange( value ) {
								props.setAttributes({ redirectType: value });
							}
						}
					),
					( function() {
						if ( props.attributes.redirectType === 'redirect_url' ) {
							return wp.element.createElement(
								wp.components.TextControl,
								{
									label: wp.i18n.__( 'Set Custom Redirect URL', 'ultimate-member' ),
									value: props.attributes.customRedirectURL,
									onChange: function onChange( value ) {
										props.setAttributes({ customRedirectURL: value });
									}
								}
							);
						}
					})()
				)
			)
		];
	},
	save: ( props ) => {
		const { attributes } = props;
        // return wp.element.createElement(
        //     wp.editor.RichText.Content,
        //     {
        //         tagName: 'p',
        //         value: 'yiyiuyiuyiuyi'
        //     }
        // );
	}
});