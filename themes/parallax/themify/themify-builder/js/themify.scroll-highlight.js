/**
 * Themify Scroll to element based on its class and highlight it when a menu item is clicked.
 * Copyright (c) Themify
 */
;(function( $, window, document ) {

	if ( ! String.prototype.trim ) {
		String.prototype.trim = function () {
			return this.replace( /^\s+|\s+$/g, '' );
		};
	}

	var pluginName = 'themifyScrollHighlight',
		defaults = {
			speed: 900,
			prefix: '.tb_section-',
			navigation: '#main-nav',
			context: 'body',
			element: '.themify_builder_row',
			scrollRate: 250,
			considerHeader: false
		};

	function Plugin( element, options ) {
		this.element = element;
		this.options = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this.init();
	}

	Plugin.prototype = {

		getOffset: function(){
			var $wpAdminBar = $('#wpadminbar'),	$headerWrap = $('#headerwrap'),
				scrollOffset = 10;
			if ( this.options.considerHeader && $headerWrap.length > 0 && $('body').hasClass('has-fixed-header') ) {
				scrollOffset = $headerWrap.outerHeight();
			}
			if ( $wpAdminBar.length > 0 ) {
				scrollOffset += $wpAdminBar.outerHeight();
			}
			return scrollOffset;
		},

		highlightLink: function( href ){
			this.dehighlightLinks();
			$(this.options.navigation).find( 'a[href=' + href + ']' ).parent().addClass( 'current_page_item' );
		},

		dehighlightLinks: function() {
			$(this.options.navigation).find( 'a[href^=#]' ).each(function(){
				$(this).parent().removeClass( 'current_page_item' );
			});
			this.removeHash('');
		},

		isInViewport: function ( obj ) {
			var $t = $(obj),
				$window = $(window),
				windowHeight = $window.height(),
				windowTop = $window.scrollTop(),
				// Divided by X to tell it's visible when the section is half way into viewport
				windowBottom = windowTop + (windowHeight/4),
				eleTop = $t.offset().top,
				eleBottom = eleTop + $t.height();
			return ((eleTop <= windowBottom) && (eleBottom >= windowTop));
		},

		isHash: function( hash ) {
			return hash && '' != hash && '#' != hash;
		},

		removeHash: function( hash ) {
			var vScroll, windowLocation = window.location;
			if ( 'replaceState' in history ) {
				history.replaceState( hash, document.title, windowLocation.pathname + windowLocation.search );
			} else {
				vScroll = document.body.scrollTop;
				windowLocation.hash = hash;
				document.body.scrollTop = vScroll;
			}
		},

		changeHash: function ( href ) {
			if ( 'replaceState' in history ) {
				history.replaceState( null, null, href );
			} else {
				var vScroll = document.body.scrollTop;
				window.location.hash = href;
				document.body.scrollTop = vScroll;
			}
		},

		linkScroll: function( obj, href ) {
			var self = this;
			// Set offset from top
			var to = $( obj ).offset().top - this.getOffset();
			// Animate scroll
			$('html,body').stop().animate({
				scrollTop: to
			}, this.options.speed, function() {
				// Highlight link
				self.highlightLink( obj.replace( self.options.prefix, '#' ) );

				if ( href != window.location.hash ) {
					// Change URL hash
					self.changeHash( href );
				}

				// Set scrolling state
				self.scrolling = false;
			});
		},

		manualScroll: function( elementsToCheck ) {
			if ( $(window).scrollTop() < 50 ) {
				this.dehighlightLinks();
			} else {
				var self = this,
					didHighlight = false;
				$.each(elementsToCheck, function( i, val ){
					if ( self.isInViewport( val ) ) {
						var elemsFirstPass = val.split('.tb_section-');
						if ( elemsFirstPass[1] ) {
							var elemsSndPass = elemsFirstPass[1].split('.' ),
								href = '';
							if ( elemsSndPass.length > 1 && elemsSndPass[0] ) {
								href = '#' + elemsSndPass[0];
							} else {
								href = '#' + elemsSndPass;
							}

							if ( href != window.location.hash ) {
								// Highlight link
								self.highlightLink( href );

								// Change URL hash
								self.changeHash( href );
							}

							// Set highlight state
							didHighlight = true;
						}
					}
				});
				if ( ! didHighlight ) {
					self.dehighlightLinks();
				}
			}
		},

		scrolling: false,

		init: function () {
			var self = this;

			// Smooth Scroll and Link Highlight
			$( this.options.context ).find( 'a[href^=#]' ).on( 'click', function (e) {
				e.preventDefault();
				// Set state
				self.scrolling = true;

				// Build class to scroll to
				var href = $(this).attr('href'),
					classToScroll = href.replace(/#/, self.options.prefix);

				// Perform scroll
				if ( 1 == $(classToScroll).length ) {
					self.linkScroll( classToScroll, href );
				}
			});
			$('#wpadminbar').find('a').off('click');


			// Highlight Link when scroll over element
			var elementsToCheck = [];
			// Build list of elements to check visibility
			$('div[class*=' + self.options.prefix.replace('.', '') + ']').not(self.options.exclude).each(function(){
				elementsToCheck.push( '.' + $(this).attr('class').trim().replace(/ /g, '.') );
			});

			// Setup scroll event
			var didScroll = false;
			$(window).scroll(function() {
				didScroll = true;
			});
			setInterval(function() {
				if ( didScroll && ! self.scrolling ) {
					didScroll = false;
					self.manualScroll( elementsToCheck );
				}
			}, self.options.scrollRate);

			// Initial section visibility check and link highlight
			$(window).load(function(){
				if ( self.isHash( window.location.hash ) ) {
					// If there's a hash, scroll to it
					var $linkHash = $( self.options.context ).find( 'a[href=' + window.location.hash + ']' );
					if ( $linkHash.length > 0 ) {
						$linkHash.trigger( 'click' );
					}  else {
						self.manualScroll( elementsToCheck );
					}
				} else {
					self.manualScroll( elementsToCheck );
				}
			});
		}
	};

	$.fn[pluginName] = function ( options ) {
		return this.each( function () {
			if ( ! $.data( this, 'plugin_' + pluginName ) ) {
				$.data( this, 'plugin_' + pluginName, new Plugin( this, options ) );
			}
		});
	};

})( jQuery, window, document );