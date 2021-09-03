<?php
/**
 * STYLE ARRAY OPTIONS
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$banner_options_default = YITH_WCMAP_Banners::get_default_banner_options();

$banners = array(

	'banners' => array(

		array(
			'title' => __( 'Banners', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-banners-options',
		),

		array(
			'id'                => 'yith_wcmap_banners',
			'name'              => __( 'Banners', 'yith-woocommerce-customize-myaccount-page' ),
			'type'              => 'yith-field',
			'yith-type'         => 'toggle-element',
			'add_button'        => __( 'Add banner', 'yith-woocommerce-customize-myaccount-page' ),
			'add_button_closed' => __( 'Close new element', 'yith-woocommerce-customize-myaccount-page' ), //optional

			'yith-display-row' => false,
			'title'            => '%%name%%',
			'elements'         => array(
				array(
					'id'        => 'name',
					'name'      => _x( 'Banner name', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => $banner_options_default['name']
				),
				array(
					'id'        => 'icon_type',
					'name'      => _x( 'Banner icon', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'options'   => array(
						'empty'   => __( 'Don\'t show an icon', 'yith-woocommerce-customize-myaccount-page' ),
						'default' => __( 'Show a default icon', 'yith-woocommerce-customize-myaccount-page' ),
						'custom'  => __( 'Upload a custom icon', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'default'   => $banner_options_default['icon_type'],
				),
				array(
					'id'        => 'icon',
					'type'      => 'yith-field',
					'yith-type' => 'select',
					'name'      => _x( 'Choose icon', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'class'     => 'icon-select',
					'options'   => array(),
					'deps'      => array(
						'id'     => 'icon_type',
						'values' => 'default',
					),
				),
				array(
					'id'        => 'custom_icon',
					'type'      => 'yith-field',
					'yith-type' => 'upload',
					'name'      => _x( 'Upload icon', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'deps'      => array(
						'id'     => 'icon_type',
						'values' => 'custom',
					),
				),
				array(
					'id'        => 'custom_icon_width',
					'name'      => _x( 'Icon width', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'number',
					'class'     => 'banner-input-number',
					'default'   => 120,
					'min'       => 1,
					'step'      => 1,
					'deps'      => array(
						'id'     => 'icon_type',
						'values' => 'default,custom',
					),
				),
				array(
					'id'        => 'width',
					'name'      => _x( 'Widget width (px)', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'number',
					'class'     => 'banner-input-number',
					'default'   => $banner_options_default['width'],
					'min'       => '1'
				),
				array(
					'id'        => 'text',
					'name'      => _x( 'Widget text', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'textarea',
					'default'   => $banner_options_default['text'],
				),
				array(
					'id'           => 'colors',
					'name'         => _x( 'Banner colors', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							'name'    => _x( 'Text', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'text',
							'default' => $banner_options_default['colors']['text'],
						),
						array(
							'name'    => _x( 'Background', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'background',
							'default' => $banner_options_default['colors']['background'],
						),
						array(
							'name'    => _x( 'Borders', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'border',
							'default' => $banner_options_default['colors']['border'],
						),
						array(
							'name'    => _x( 'Text hover', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'text_hover',
							'default' => $banner_options_default['colors']['text_hover'],
						),
						array(
							'name'    => _x( 'Background hover', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'background_hover',
							'default' => $banner_options_default['colors']['background_hover'],
						),
						array(
							'name'    => _x( 'Borders hover', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'border_hover',
							'default' => $banner_options_default['colors']['border_hover'],
						),
					),
				),
				array(
					'id'        => 'show_counter',
					'name'      => _x( 'Show badge with dynamic count of items', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'default'   => $banner_options_default['show_counter'],
				),
				array(
					'id'        => 'counter_type',
					'name'      => _x( 'Show badge with dynamic count of items', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'select',
					'options'   => apply_filters( 'yith_wcmap_banner_counter_type_options', array(
						'downloads' => _x( 'Downloads', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' ),
						'orders'    => _x( 'Orders', 'Banner counter option', 'yith-woocommerce-customize-myaccount-page' ),
					) ),
					'deps'      => array(
						'id'    => 'show_counter',
						'value' => 'yes'
					),
				),
				array(
					'id'           => 'counter_colors',
					'name'         => _x( 'Badge colors', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'         => 'yith-field',
					'yith-type'    => 'multi-colorpicker',
					'colorpickers' => array(
						array(
							'name'    => _x( 'Background', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'background',
							'default' => $banner_options_default['counter_colors']['background'],
						),
						array(
							'name'    => _x( 'Text', '[admin]Plugin option color label', 'yith-woocommerce-customize-myaccount-page' ),
							'id'      => 'text',
							'default' => $banner_options_default['counter_colors']['text'],
						),
					),
					'deps'         => array(
						'id'    => 'show_counter',
						'value' => 'yes'
					),
				),
				array(
					'id'        => 'link',
					'name'      => _x( 'Banner links', '[admin]Plugin option label', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'options'   => array(
						'empty'    => __( 'Don\'t add a link', 'yith-woocommerce-customize-myaccount-page' ),
						'endpoint' => __( 'To a specific endpoint', 'yith-woocommerce-customize-myaccount-page' ),
						'url'      => __( 'To an external url', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'default'   => $banner_options_default['link'],
				),
				array(
					'id'        => 'link_endpoint',
					'name'      => __( 'Banner links to', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'select',
					'options'   => yith_wcmap_endpoints_list(),
					'default'   => $banner_options_default['link_endpoint'],
					'deps'      => array(
						'id'    => 'link',
						'value' => 'endpoint'
					),
				),
				array(
					'id'        => 'link_url',
					'name'      => __( 'Banner links to', 'yith-woocommerce-customize-myaccount-page' ),
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => $banner_options_default['link_url'],
					'deps'      => array(
						'id'    => 'link',
						'value' => 'url'
					),
				),
			),
			'sortable'         => false, //optional
			'save_button'      => array(
				'id'   => 'save',
				'name' => _x( 'Save', 'Save single banner button label', 'yith-woocommerce-customize-myaccount-page' ),
			), //optional but mandatory if is defined 'add_button'
			'delete_button'    => array(
				'id'   => 'delete',
				'name' => _x( 'Delete', 'Delete single banner button label', 'yith-woocommerce-customize-myaccount-page' ),
			), //optional
			'default'          => YITH_WCMAP_Banners::get_default_banners()
		),


		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcmap-end-banners-options',
		),
	),
);

return apply_filters( 'yith_wcmap_panel_banners_options', $banners );