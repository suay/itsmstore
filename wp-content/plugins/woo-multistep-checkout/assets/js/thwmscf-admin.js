var thwmscf_admin = (function($){
	$(function(){
		$( ".thpladmin-colorpick" ).each(function() {     	
			var value = $(this).val();
			$( this ).parent().find( '.thpladmin-colorpickpreview' ).css({ backgroundColor: value });
		});

	    $('.thpladmin-colorpick').iris({
			change: function( event, ui ) {
				$( this ).parent().find( '.thpladmin-colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
			},
			hide: true,
			border: true
		}).click( function() {
			$('.iris-picker').hide();
			$(this ).closest('td').find('.iris-picker').show(); 
		});

		$('body').click( function() {
			$('.iris-picker').hide();
		});

		$('.thpladmin-colorpick').click( function( event ) {
			event.stopPropagation();
		});
	});

	function backtocart(elm){
		var cart_text_settings = $('.back-to-cart-show');		
		if(elm.checked){
			cart_text_settings.show();;
		}else{
			cart_text_settings.hide();
		}
	}

	function displaylogin(elm){
		var cart_text_settings = $('.display-login-step');		
		if(elm.checked){
			cart_text_settings.show();;
		}else{
			cart_text_settings.hide();
		}
	}

	function shippingtitle(elm) {
		var shipping_title = $('.display-shipping-title')
		if(elm.checked){
			shipping_title.hide();
		}else{
			shipping_title.show();
		}
	}

	function layoutchange(elm){
		var layout = elm.value;
		var tab_position = $('.display-tab-position');
		if(layout == 'thwmscf_time_line_step'){   	
			var color = '#050505';
			var text_color_active = $('input[name="i_step_text_color_active"]');
			if(text_color_active.val() == '#ffffff'){
				text_color_active.parent().find( '.thpladmin-colorpickpreview' ).css({ backgroundColor: color });
				text_color_active.val(color);
			}
			tab_position.hide();
		}else{
			tab_position.show();
		}
	}

	return {
		backtocart : backtocart,
		displaylogin : displaylogin,
		shippingtitle : shippingtitle,
		layoutchange : layoutchange,
	}
}(window.jQuery, window, document))

function thwmscfBackToCart(elm){
	thwmscf_admin.backtocart(elm);
}
function thwmscfDisplayLogin(elm){
	thwmscf_admin.displaylogin(elm);
}
function thwmscfShippingTitle(elm){
	thwmscf_admin.shippingtitle(elm);
}
function thwmscLayoutChange(elm){
	thwmscf_admin.layoutchange(elm);
}		