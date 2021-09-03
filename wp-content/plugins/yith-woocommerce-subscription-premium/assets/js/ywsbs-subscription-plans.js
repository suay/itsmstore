( function( blocks, element, i18n ) {
	var el = element.createElement;
	var yith_icon = el('svg', {width: 22, height: 22},
		el('path', {d: "M 18.24 7.628 C 17.291 8.284 16.076 8.971 14.587 9.688 C 15.344 7.186 15.765 4.851 15.849 2.684 C 15.912 0.939 15.133 0.045 13.514 0.003 C 11.558 -0.06 10.275 1.033 9.665 3.284 C 10.007 3.137 10.359 3.063 10.723 3.063 C 11.021 3.063 11.267 3.184 11.459 3.426 C 11.651 3.668 11.736 3.947 11.715 4.262 C 11.695 5.082 11.276 5.961 10.46 6.896 C 9.644 7.833 8.918 8.3 8.282 8.3 C 7.837 8.3 7.625 7.922 7.646 7.165 C 7.667 6.765 7.804 5.955 8.056 4.735 C 8.287 3.579 8.403 2.801 8.403 2.401 C 8.403 1.707 8.224 1.144 7.867 0.713 C 7.509 0.282 6.994 0.098 6.321 0.161 C 5.858 0.203 5.175 0.624 4.27 1.422 C 3.596 2.035 2.923 2.644 2.25 3.254 L 2.976 4.106 C 3.564 3.664 3.922 3.443 4.048 3.443 C 4.448 3.443 4.637 3.717 4.617 4.263 C 4.617 4.306 4.427 4.968 4.049 6.251 C 3.671 7.534 3.471 8.491 3.449 9.122 C 3.407 9.985 3.565 10.647 3.924 11.109 C 4.367 11.677 5.106 11.919 6.142 11.835 C 7.366 11.751 8.591 11.298 9.816 10.479 C 10.323 10.142 10.808 9.753 11.273 9.311 C 11.105 10.153 10.905 10.868 10.673 11.457 C 8.402 12.487 6.762 13.37 5.752 14.107 C 4.321 15.137 3.554 16.241 3.449 17.419 C 3.259 19.459 4.29 20.479 6.541 20.479 C 8.055 20.479 9.517 19.554 10.926 17.703 C 12.125 16.126 13.166 14.022 14.049 11.394 C 15.578 10.635 16.87 9.892 17.928 9.164 C 17.894 9.409 18.319 7.308 18.24 7.628 Z  M 7.393 16.095 C 7.056 16.095 6.898 15.947 6.919 15.653 C 6.961 15.106 7.908 14.38 9.759 13.476 C 8.791 15.221 8.002 16.095 7.393 16.095 Z"})
	);

	var blockStyle = {
		backgroundColor: '#900',
		color: '#fff',
		padding: '20px',
	};

	blocks.registerBlockType( 'yith/ywsbs-subscription-plans', {
		title: i18n.__('Subscription Plans', 'yith-woocommerce-subscription'),
		icon: yith_icon,
		category: 'yith-blocks',
		example: {},
		edit: function() {
			return el(
				'p',
				{ style: blockStyle },
				'Hello World, step 1 (from the editor).'
			);
		},
		save: function() {
			return el(
				'p',
				{ style: blockStyle },
				'Hello World, step 1 (from the frontend).'
			);
		},
	} );
}(
	window.wp.blocks,
	window.wp.element,
	window.wp.i18n
) );
/*
import {__} from '@wordpress/i18n';
import {registerBlockType} from '@wordpress/blocks';
import {Text} from '@wordpress/components';
import {ProductButton} from '@woocommerce/atomic-components/product';



const blockConfig = {
	title: __('Subscription Plans', 'yith-woocommerce-subscription'),
	description: __(
		'Add subscription table price',
		'yith-woocommerce-subscription'
	),
	icon: yith_icon,
	category: 'layout',

	edit() {
		return <div> Hello World </div>;
	},
	save() {
		return <div> Hello World </div>;
};

registerBlockType('yith/ywsbs-subscription-plans', {
	...blockConfig,
});
*/