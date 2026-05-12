( function ( wp ) {
	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.blockEditor || ! wp.components || ! wp.i18n ) {
		return;
	}

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Notice = wp.components.Notice;

	wp.blocks.registerBlockType( 'baf/travelpayouts-widget', {
		apiVersion: 2,
		title: __( 'Travelpayouts Widget', 'bookings-flights-core' ),
		description: __( 'Render an approved Bookings and Flights travel placement by registry key.', 'bookings-flights-core' ),
		category: 'widgets',
		icon: 'airplane',
		attributes: {
			placement: {
				type: 'string',
				default: '',
			},
			surface: {
				type: 'string',
				default: '',
			},
			channel: {
				type: 'string',
				default: '',
			},
			slug: {
				type: 'string',
				default: '',
			},
		},
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'baf-travelpayouts-widget-editor',
			} );

			return el(
				'div',
				blockProps,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{
							title: __( 'Placement', 'bookings-flights-core' ),
							initialOpen: true,
						},
						el( TextControl, {
							label: __( 'Placement key', 'bookings-flights-core' ),
							value: attributes.placement,
							onChange: function ( value ) {
								setAttributes( { placement: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Surface', 'bookings-flights-core' ),
							value: attributes.surface,
							onChange: function ( value ) {
								setAttributes( { surface: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Channel', 'bookings-flights-core' ),
							value: attributes.channel,
							onChange: function ( value ) {
								setAttributes( { channel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Slug', 'bookings-flights-core' ),
							value: attributes.slug,
							onChange: function ( value ) {
								setAttributes( { slug: value } );
							},
						} )
					)
				),
				el(
					Notice,
					{
						status: attributes.placement ? 'info' : 'warning',
						isDismissible: false,
					},
					attributes.placement
						? __( 'This block renders the approved placement on the frontend.', 'bookings-flights-core' )
						: __( 'Add an approved placement key before publishing.', 'bookings-flights-core' )
				),
				attributes.placement
					? el(
						'code',
						{},
						'[baf_travelpayouts_widget placement="' + attributes.placement + '"]'
					)
					: null
			);
		},
		save: function () {
			return null;
		},
	} );
}( window.wp ) );
