/* Routines to manage font icons in theme settings and custom panel. */

;var Themify_Icons = {};

(function($){

	Themify_Icons = {

		target: '',

		getDocHeight: function() {
			var D = document;
			return Math.max(
				Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
				Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
				Math.max(D.body.clientHeight, D.documentElement.clientHeight)
			);
		},

		showLightbox: function( selected ) {
			var top = $(document).scrollTop() + 80,
				$lightbox = $("#themify_lightbox_fa"),
				$lightboxOverlay = $('#themify_lightbox_overlay');

			$lightboxOverlay.show();
			$lightbox
			.show()
			.css('top', Themify_Icons.getDocHeight())
			.animate({
				'top': top
			}, 800 );
			if( selected ) {
				$('a', $lightbox)
				.removeClass('selected')
				.find('.' + selected)
				.closest('a')
					.addClass('selected');
			}

			// Position lightbox correctly in Builder
			if ( $('body').hasClass('themify_builder_active') && $('body').hasClass('frontend') ) {
				var $tbOverlay = $('#themify_builder_overlay');
				if ( $tbOverlay.length > 0 ) {
					$lightboxOverlay.insertAfter($tbOverlay);
					$lightbox.insertAfter($tbOverlay);
				}
			}
		},

		setIcon: function(iconName) {
			var $target = $(Themify_Icons.target);
			$target.val( iconName );
			if ( $('.fa:not(.icon-close)', $target.parent().parent()).length > 0 ) {
				$('.fa:not(.icon-close)', $target.parent().parent()).removeClass().addClass( 'fa ' + iconName );
			}
			Themify_Icons.closeLightbox();
		},

		closeLightbox: function() {
			$('#themify_lightbox_fa').animate({
				'top': Themify_Icons.getDocHeight()
			}, 800, function() {
				$('#themify_lightbox_overlay').hide();
				$('#themify_lightbox_fa').hide();
			});
		}

	};

	$(document).ready(function(){
		var $body = $('body');

		jQuery( '#themify_lightbox_overlay, #themify_lightbox_fa' ).appendTo( 'body' );

		$body.on('click', '.themify_fa_toggle', function(e){
			e.preventDefault();
			var thiz = $( this );
			if( thiz.attr('data-target') ) {
				Themify_Icons.target = $( thiz.attr('data-target') );
			} else {
				Themify_Icons.target = thiz.prev();
			}
			Themify_Icons.showLightbox( Themify_Icons.target.val() );
		});

		$body.on('click', '#themify_lightbox_fa .lightbox_container a', function(e){
			e.preventDefault();
			Themify_Icons.setIcon( $(this).attr('data-name') );
		});

		$body.on('click', '#themify_lightbox_overlay, #themify_lightbox_fa .close_lightbox', function(e){
			e.preventDefault();
			Themify_Icons.closeLightbox();
		});
	});

})(jQuery);