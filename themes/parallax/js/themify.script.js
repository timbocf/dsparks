;// Themify Theme Scripts - http://themify.me/

// Themify Lightbox, Fixed Header and Parallax /////////////////////////
var ThemifyGallery, FixedHeader, ThemifyParallax;

// Begin jQuery functions
(function($) {

	function UpdateQueryString(a,b,c){
		c||(c=window.location.href);var d=RegExp("([?|&])"+a+"=.*?(&|#|$)(.*)","gi");if(d.test(c))return b!==void 0&&null!==b?c.replace(d,"$1"+a+"="+b+"$2$3"):c.replace(d,"$1$3").replace(/(&|\?)$/,"");if(b!==void 0&&null!==b){var e=-1!==c.indexOf("?")?"&":"?",f=c.split("#");return c=f[0]+e+a+"="+b,f[1]&&(c+="#"+f[1]),c}return c;
	}

	// Scroll to Element //////////////////////////////
	function themeScrollTo(offset, duration) {
		duration = duration || 800;
		$('html, body').stop().animate({ scrollTop: offset }, duration);
	}

	// Themify Scroll
	(function($, window, document, undefined) {

		// Create the defaults once
		var pluginName = "themifyScroll", defaults = {
			speed : 1800
		};

		// The actual plugin constructor
		function Plugin(element, options) {
			this.element = element;
			this.options = $.extend({}, defaults, options);
			this._defaults = defaults;
			this._name = pluginName;
			this.onClicking = false;
			this.init();
		}

		Plugin.prototype = {
			sections: [],
			init : function() {
				var self = this,
					IE = this.ie();

				self.sections = $('#main-nav li a').map(function(){
					return this.hash || null;
				}).get();
				self.sections.push('#header');

				// click event
				$('body').on('click', 'a[href*=#]', function(e) {
					var $this = $(this), elementClick = $this.prop('hash');
					
					if ( 'undefined' != typeof elementClick && elementClick.indexOf('#') != -1 && elementClick.length > 1 ) {
						var destination = $(elementClick).offset().top + 10, newHash = '#' + elementClick.replace('#', '');
						if( $(elementClick).length === 0 ) { return; }

						// update state
						self.onClicking = true;

						e.preventDefault();
						$("html,body").animate({
							scrollTop: destination
						}, self.options.speed, function(){
							// add active class
							$this.parent('li').addClass('current_page_item').siblings().removeClass('current_page_item');

							if(elementClick.replace('#','') !== 'header'){
								self.setHash(newHash);
							} else {
								self.clearHash();
							}

							self.onClicking = false;
						});

						// close mobile menu
						if($(window).width() <= 760 && $('#main-nav').is(':visible')){
							$('#menu-icon').trigger('click');
						}

					}

				});

				// change hash url
				if( !IE || (IE && IE > 9) ) {
					this.changeHash();
				}
			},

			changeHash : function() {
				var self = this;

				if (self.sections.length > 0) {
					var didScroll = false,
						currentHash;
					$(window).scroll(function() {
						didScroll = true;
					});

					setInterval(function() {

						if(self.onClicking) return;

						if ( didScroll ) {
							didScroll = false;
							currentHash = window.location.hash.replace(/^#\!\//, "");

							if ( $(window).scrollTop() == 0 && currentHash != '' ) {
								self.clearHash();
							}

							$.each(self.sections, function(index, value){
								var section = value, obj = $(value);
								if (obj.length > 0) {
									var offsetY = obj.offset().top,
										elemHeight = obj.outerHeight();

									var offsetAmount = offsetY;
									
									if ( offsetAmount < window.pageYOffset && offsetAmount + elemHeight > window.pageYOffset ) {
										if( section == currentHash || section == '#header' ) return;
										
										self.setHash(section);
										$('a[href*='+section+']').parent('li').addClass('current_page_item').siblings().removeClass('current_page_item current-menu-item');
										
									}
								}
							});
						}
						
					}, 500);
				}
			},

			setHash: function(hash) {
				if(history.pushState) {
					history.pushState(null, null, hash);
				}
				else {
					location.hash = hash;
				}
			},

			clearHash: function() {
				// remove hash
				if ( window.history && window.history.replaceState ) { 
					window.history.replaceState('', '', window.location.pathname); 
				} else { 
					window.location.href = window.location.href.replace(/#.*$/, '#'); 
				}
			},

			ie: function() {
				var undef,
					v = 3,
					div = document.createElement('div'),
					all = div.getElementsByTagName('i');
				
				while (
					div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
					all[0]
				);
				
				return v > 4 ? v : undef;
			}
		};

		$.fn[pluginName] = function(options) {
			return this.each(function() {
				if (!$.data(this, "plugin_" + pluginName)) {
					$.data(this, "plugin_" + pluginName, new Plugin(this, options));
				}
			});
		};

	})(jQuery, window, document);

	// Test if touch event exists //////////////////////////////
	function is_touch_device() {
		return ('true' == themifyScript.isTouch) || !!('window.navigator.msMaxTouchPoints' in window);
	}

	function is_ie() {
		return navigator.appName == 'Microsoft Internet Explorer';
	}

	// Start infinite scroll and isotope
	function infiniteIsotope(containerSelector, itemSelectorIso, itemSelectorInf, containerInfinite, doIso, isoColW) {

		// Get max pages for regular category pages and home
		var scrollMaxPages = parseInt(themifyScript.maxPages), $container = $(containerSelector), $containerInfinite = $(containerInfinite);
		// Get max pages for Query Category pages
		if ( typeof qp_max_pages != 'undefined')
			scrollMaxPages = qp_max_pages;

		if ((!$('body.list-post').length > 0) && doIso) {
			// isotope init
			$container.isotope({
				masonry : {
					columnWidth : isoColW
				},
				itemSelector : itemSelectorIso,
				transformsEnabled : false,
				animationEngine : 'jquery'
			});
			$(window).resize();
		}

		// infinite scroll
		$containerInfinite.infinitescroll({
			navSelector : '#load-more a:last', // selector for the paged navigation
			nextSelector : '#load-more a:last', // selector for the NEXT link (to page 2)
			itemSelector : itemSelectorInf, // selector for all items you'll retrieve
			loadingText : '',
			donetext : '',
			loading : {
				img : themifyScript.loadingImg
			},
			maxPage : scrollMaxPages,
			behavior : 'auto' != themifyScript.autoInfinite ? 'twitter' : '',
			pathParse : function(path, nextPage) {
				return path.match(/^(.*?)\b2\b(?!.*\b2\b)(.*?$)/).slice(1);
			}
		}, function(newElements) {
			// call Isotope for new elements
			var $newElems = $(newElements).hide();

			// Mark new items: remove newItems from already loaded items and add it to loaded items
			$('.post.newItems').removeClass('newItems');
			$newElems.addClass('newItems');

			$newElems.imagesLoaded(function() {

				$newElems.fadeIn();

				$('#infscr-loading').fadeOut('normal');
				if (1 == scrollMaxPages) {
					$('#load-more, #infscr-loading').remove();
				}

				// Apply lightbox/fullscreen gallery to new items
				if(typeof ThemifyGallery !== 'undefined'){ ThemifyGallery.init({'context': jQuery(themifyScript.lightboxContext)}); }

				if ( typeof jQuery.fn.carouFredSel !== 'undefined') {
					// Create carousel on portfolio new items
					createCarousel( $( '.type-portfolio.newItems .slideshow' ), 'normal' );
				}

				if ((!$('body.list-post').length > 0) && doIso) {
					$container.isotope('appended', $newElems);
				}

				// Apply Transitin Effect
				if( themifyScript.transitionEffect ) {
					ThemifyParallax.parallaxElements();
					if ( 'undefined' !== typeof ThemifyBuilderModuleJs ) {
						ThemifyBuilderModuleJs.doAnimation();
					}
				}
			});

			scrollMaxPages = scrollMaxPages - 1;
			if (1 < scrollMaxPages && 'auto' != themifyScript.autoInfinite)
				$('#load-more a').show();
		});

		// disable auto infinite scroll based on user selection
		if ('auto' == themifyScript.autoInfinite) {
			$('#load-more, #load-more a').hide();
		}

	}

	// Initialize carousels
	function createCarousel(obj, mode) {
		obj.each(function() {
			var $this = $(this),
				this_id = $this.data('id');
			$this.carouFredSel({
				responsive : true,
				prev : '#' + this_id + ' .carousel-prev',
				next : '#' + this_id + ' .carousel-next',
				pagination : {
					container : '#' + this_id + ' .carousel-pager'
				},
				circular : true,
				infinite : true,
				swipe: true,
				scroll : {
					items : 1,
					fx : $this.data('effect'),
					duration : parseInt($this.data('speed'))
				},
				auto : {
					play : ('off' != $this.data('autoplay')),
					timeoutDuration : 'off' != $this.data('autoplay') ? parseInt($this.data('autoplay')) : 0
				},
				items : {
					visible : {
						min : 1,
						max : 1
					},
					width : 222
				},
				onCreate : function() {
					$this.closest('.slideshow-wrap').css({
						'visibility' : 'visible',
						'height' : 'auto'
					});
					$('.carousel-next, .carousel-prev', $this.closest('.slideshow-wrap')).hide();
					$(window).resize();
				}
			});
		});
	}

	// Fixed Header /////////////////////////
	FixedHeader = {
		init : function() {
			this.headerHeight = $('#header hgroup').outerHeight() - $('#nav-bar').outerHeight();

			if ( $('body').hasClass('ie') ) { $('html, body').addClass('iefix'); }

			$(window).on('scroll', this.activate)
			.on('touchstart.touchScroll', this.activate)
			.on('touchmove.touchScroll', this.activate);
		},
		activate : function() {

			if ( $(window).scrollTop() <= FixedHeader.headerHeight ) {
				FixedHeader.scrollDisabled();
			} else {
				FixedHeader.scrollEnabled();
			}
		},
		scrollDisabled : function() {
			$('#nav-bar').removeClass('fixed-nav-bar');
			$('body').removeClass('fixed-header-on');
		},
		scrollEnabled : function() {
			$('#nav-bar').addClass('fixed-nav-bar');
			$('body').addClass('fixed-header-on');
		}
	};

	// Parallax /////////////////////////
	ThemifyParallax = {
		init: function() {
			this.windowHeight = $(window).height();
			$(window).resize(function(){
				ThemifyParallax.windowHeight = $(window).height();
			});

			if(themifyScript.scrollingEffect) {
				if(themifyScript.scrollingEffectType == 'effect1'){
					this.setup_effect_1();
				} else {
					this.setup_effect_2();
				}
			} else {
				this.setBackground();
			}
		},

		setup_effect_1: function() {
			var self = ThemifyParallax;

			self.setBackground(); // set background fullcover

			// only work in query section
			if ( ! $('body').hasClass('query-section') || is_ie() ){ return; }
			
			$('.section-post, #headerwrap').each(function(){
				var ids = $(this).prop('id'),
					$elemns = $('#' + ids ),
					didScroll = false;

				setInterval(function() {
					if ( didScroll ) {
						didScroll = false;
						self.run($elemns);
					}
				}, 550);

				$(window).on('scroll touchstart.touchScroll touchmove.touchScroll', function(){
					didScroll = true;
				});
			});
		},

		setup_effect_2: function(){
			$('.section-post, #headerwrap').each(function(){
				// Store some variables based on where we are
				var $self = $(this),
					bgImage = typeof $self.data('bg') !== 'undefined' ? 'url("'+ $self.data('bg') +'")' : '';

				$self.addClass('scrolling-bg-image').css({
					backgroundImage: bgImage
				});

				if( typeof $.fn.builderParallax !== 'undefined' ){
					$self.builderParallax("50%", 0.1);
				}
			});
		},

		run: function(elemns) {
			var $window = $(window),
				pos = $window.scrollTop(),
				$element = elemns,
				top = $element.offset().top,
				height = $element.outerHeight(true);	

			// Check if totally above or totally below viewport
			if (top + height < pos || top > pos + ThemifyParallax.windowHeight) {
				return;
			}
			var cssTopPos = Math.max(Math.round((pos - top) * 0.2), 0) + 'px';

			$element.css('top', cssTopPos);

			if($('#nav-bar').hasClass('fixed-nav-bar')) {
				$('#nav-bar').css({'bottom': ''});
			}
		},

		newPos: function( pos, adjust, ratio ) {
			return ((pos - adjust) * ratio)  + "px";
		},

		parallaxElements: function(){
			// shortcode columns add class
			$('.col2-1.first, .col3-1.first, .col3-2.first, .col4-1.first, .col4-2.first, .col4-3.first', $('#body, #footer')).each(function(){
				var $this = $(this);
				if($this.hasClass('col2-1')) {
					$this.next('.col2-1').addClass('last');
					$this.next('.col4-1').addClass('third').next('.col4-1').addClass('last');
				} else if($this.hasClass('col3-1')) {
					$this.next('.col3-1').addClass('second').next('.col3-1').addClass('last');
					$this.next('.col3-2').addClass('last');
				} else if($this.hasClass('col3-2')) {
					$this.next('.col3-1').addClass('last');
				} else if($this.hasClass('col4-1')) {
					$this.next('.col4-1').addClass('second').next('.col4-1').addClass('third').next('.col4-1').addClass('last');
					$this.next('.col4-1').next('.col4-2').addClass('last');
				} else if($this.hasClass('col4-2')) {
					$this.next('.col4-2').addClass('last');
					$this.next('.col4-1').addClass('third').next('.col4-1').addClass('last');
				} else if($this.hasClass('col4-3')) {
					$this.next('.col4-1').addClass('last');
				}
			});
			// gallery transition classes
			$('.gallery', $('#body')).each(function(){
				var columns = $(this).attr('class').match(/gallery-columns-([0-9])/)[1];
				$('dl', $(this)).addClass('middle');
				$('dl:nth-of-type('+columns+'n+1)', $(this)).addClass('first');
				$('dl:nth-of-type('+columns+'n+1)', $(this)).removeClass('middle');
				$('dl:nth-of-type('+columns+'n+'+columns+')', $(this)).addClass('last');
				$('dl:nth-of-type('+columns+'n+'+columns+')', $(this)).removeClass('middle');
				if(columns > 3) {
					$('dl:nth-of-type('+columns+'n+2)', $(this)).addClass('second');
					$('dl:nth-of-type('+columns+'n+2)', $(this)).removeClass('middle');
					$('dl:nth-of-type('+columns+'n+'+(columns-1)+')', $(this)).addClass('third');
					$('dl:nth-of-type('+columns+'n+'+(columns-1)+')', $(this)).removeClass('middle');
				}
			});

			$.each(themifyScript.transitionSetup.selectors, function(key, val){
				$(val).addClass(themifyScript.transitionSetup.effect);
			});
		},

		setBackground: function(){
			// Fullscreen bg
			if ( typeof $.fn.backstretch !== 'undefined') {
				var $section = $('.section-post, #headerwrap'),
					resizeId;
				$section.each(function() {
					var bg = $(this).data('bg');
					if ( typeof bg !== 'undefined') {
						if ($(this).hasClass('fullcover')) {
							$(this).backstretch(bg);
						} else {
							$(this).css('background-image', 'url(' + bg + ')');
						}
					}
				});

				$(window).on('backstretch.show', function(e, instance) {
					instance.$container.css('z-index', '');
				}).on('resize', function(){
					clearTimeout(resizeId);
					resizeId = setTimeout(function(){
						$section.each(function(){
							if($(this).hasClass('fullcover')){
								var instance = $(this).data("backstretch");
								if('undefined' !== typeof instance) instance.resize();
							}
						});
					}, 500);
				});
			}
		}
	};

	// portfolio ajax post break point
	function portfolio_item_break_point() {
		// section portfolio break point
		$('.shortcode.portfolio, .module.module-portfolio').each(function(){
			var selector = '', viewport = $(window).width();

			// reset
			$(this).find('.sec-post-break').remove();

			if($(this).hasClass('grid2-thumb') || $(this).hasClass('grid2')){
				selector = '.post:nth-of-type(2n+2)';
			} else if($(this).hasClass('grid4')) {
				selector = '.post:nth-of-type(4n+4)';
			} else if($(this).hasClass('grid3')){
				selector = '.post:nth-of-type(3n+3)';
			} else if($(this).hasClass('grid2')) {
				selector = '.post:nth-of-type(2n+2)';
			} else {
				selector = '.post:nth-of-type(1n+1)';
			}

			// mobile
			if(viewport < 480) {
				selector = '.post:nth-of-type(1n+1)';
			}

			$(this).find(selector).each(function(){
				$('<div class="sec-post-break" />').insertAfter($(this));
			});
		});
	}

	// Returns real dimensions from scaled or hidden elements
	function realDimension($obj, dimension) {
		var $clone = $obj.clone().css('visibility', 'hidden').appendTo($('body') ),
			value = 'width' == dimension ? $clone.width() : $clone.height();
		$clone.remove();
		return value;
	}

	// Execute Earlier than $.ready and $.load
	if ( themifyScript.transitionEffect ) {
		ThemifyParallax.parallaxElements();
	}

	/////////////////////////////////////////////
	// Execute when DOM is ready
	/////////////////////////////////////////////
	$(document).ready(function() {

		var $body = $('body'),
			$skills = $('.progress-bar', $body);

		// Toggle main nav on mobile
		$body.on('click', '#menu-icon', function(event){
			event.preventDefault();
			$("#main-nav").fadeToggle();
			$("#headerwrap #top-nav").hide();
			$(this).toggleClass("active");
		});

		// Back to top
		$('.not-ie .back-top a').on('click', function(e){
			e.preventDefault();
			themeScrollTo(0);
		});

		// If is touch device, add class
		if (is_touch_device()) {
			$body.addClass('is-touch');
			var vh = $(window).height();
			if(navigator.userAgent.match(/iPhone/i)) {
				vh = vh + 60;
			}
			if ( $body.hasClass('home') ) {
				$('.query-section #headerwrap').css('height', vh);
			}
		}
		
		// Show/Hide direction arrows
		$('#body').on('mouseover mouseout', '.slideshow-wrap', function(event) {
			if (event.type == 'mouseover') {
				if( $(window).width() > 600 ){
					$('.carousel-next, .carousel-prev', $(this)).css('display', 'block');
				}
			} else {
				if( $(window).width() > 600 ){
					$('.carousel-next, .carousel-prev', $(this)).css('display', 'none');
				}
			}
		});

		// Fixed header
		FixedHeader.init();

		// Parallax section background
		ThemifyParallax.init();

		// Height of top bar
		var menuHeight = $('#nav-bar').outerHeight();

		// portfolio breakpoint
		portfolio_item_break_point();

		// Portfolio expand js
		var portoInitialPos;
		$('.porto-expand-js').on('click', function(e){
			e.preventDefault();
			var $this = $(this),
				prefixLink = $this.data('prefix-link'),
				url = UpdateQueryString( 'porto_expand', 1, $this.attr('href') ),
				placehold = $this.closest('.post').nextAll('.sec-post-break').first();

			if ( placehold.length === 0 ) placehold = $this.closest('.shortcode');
			if ( placehold.length === 0 ) placehold = $this.closest('.module-portfolio');

			// cache the initial position
			portoInitialPos = $(window).scrollTop();

			$('.portfolio-expanded').remove();
			$('<div class="portfolio-expanded"><div class="portfolio-expand-scaler"><iframe id="portfolio-lightbox-iframe" frameborder="0" allowfullscreen></iframe></div></div>')
			.appendTo(placehold).hide();
			$('.portfolio-loader').remove();
			$('<div class="portfolio-loader">').appendTo($this.closest('.portfolio-post').find('.post-image')).show();

			$('.portfolio-expanded').show();
			$('#portfolio-lightbox-iframe').prop('src', url).load(function(){
				var $contents = $(this).contents();
				$('.portfolio-loader').remove();
				$('.portfolio-expanded').hide().slideDown('slow');
				themeScrollTo( $('.portfolio-expanded').offset().top - menuHeight, 1000 );
				$body.trigger('portfolio_expanded');

				$contents.find('.close-expanded').on('click', function(e){
					e.preventDefault();
					$('.portfolio-expanded').slideUp(800, function(){
						$(this).remove();
					});
					themeScrollTo(portoInitialPos);
				});
			}).iframeAutoHeight();
		});

		// portfolio close and #main-nav
		$body.on('portfolio_expanded', function(){
			if ( 'undefined' !== typeof ThemifyBuilderModuleJs) ThemifyBuilderModuleJs.loadOnAjax(); // initialize builder module js
		}).on('touchstart touchmove touchend', '#main-nav', function(e) {
			e.stopPropagation();
		});

		/////////////////////////////////////////////
		// Chart Initialization
		/////////////////////////////////////////////
		if( typeof $.fn.easyPieChart !== 'undefined' ) {
			$.each(themifyScript.chart, function(index, value){
				if( 'false' == value || 'true' == value ){
					themifyScript.chart[index] = 'false'!=value;
				} else if( parseInt(value) ){
					themifyScript.chart[index] = parseInt(value);
				} else if( parseFloat(value) ){
					themifyScript.chart[index] = parseFloat(value);
				}
			});
			$('.chart', $body).each(function(){
				var $self = $(this),
					barColor = $self.data('color'),
					percent = $self.data('percent');

				if( typeof barColor !== 'undefined' ) {
					themifyScript.chart.barColor = '#' + barColor.toString().replace('#', '');

					$self.easyPieChart( themifyScript.chart );
					$self.data('easyPieChart').update(0);
					if( typeof $.waypoints !== 'undefined' && themifyScript.transitionEffect ) {
						$self.waypoint(function(direction){
							$self.data('easyPieChart').update(percent);
						}, {offset: '80%'});
						$self.waypoint(function(direction){
							if(direction === 'up') {
								$self.data('easyPieChart').update(0);
							}
						}, {offset: '92%'});
					} else {
						$self.data('easyPieChart').update(percent);
					}
				}
			});
		}
		$('.no-chart', $body).not('.module-feature').each(function () {
			var $self  = $(this),
				$img   = $self.find('img'),
				width  = themifyScript.chart.size,
				height = themifyScript.chart.size;

			if ( $img.length > 0 ) {
				width  = realDimension($img, 'width');
				height = realDimension($img, 'height');
			}

			$self.css({
				'width' : width + 'px',
				'height': height + 'px'
			}).addClass('no-chart-ready');
		});
		
		/////////////////////////////////////////////
		// Skillset Animation
		/////////////////////////////////////////////
		if( themifyScript.transitionEffect ) {
			$skills.each(function(){
				var $self = $(this).find('span'),
					percent = $self.data('percent');

				if( typeof $.waypoints !== 'undefined' ) {
					$self.waypoint(function(direction){
						$self.animate({width: percent}, 800);
					}, {offset: '80%'})
					.waypoint(function(direction){
						if(direction === 'up') {
							$self.animate({width: percent}, 800);
						}
					}, {offset: '20%'})
					.waypoint(function(direction){
						if(direction === 'up') {
							$self.width(0);
						}
					}, {offset: -10})
					.waypoint(function(direction){
						if(direction === 'down') {
							$self.width(0);
						}
					}, {offset: '110%'});
				}
			});
		}

		// Lightbox / Fullscreen initialization ///////////
		if(typeof ThemifyGallery !== 'undefined'){ ThemifyGallery.init({'context': jQuery(themifyScript.lightboxContext)}); }

		$(window).on('resize', function () {
			// Section Type Message Vertical Height
			var $context = $('#loops-wrapper'),
				$verticalSection = $('.section-post.message', $context);
				
			for (var i = 0; i < $verticalSection.length; i++) {
				var $selection = $verticalSection.eq(i);
				$selection.css({'lineHeight': parseInt( $verticalSection.css('height') ) + 'px'});
				if($selection.find('.vertical-centered').length == 0){
					$selection.find('.section-inner').wrapInner('<div class="vertical-centered" />');
				}
			}
		});
	});

	/////////////////////////////////////////////
	// Execute when page is fully loaded
	/////////////////////////////////////////////
	$(window).load(function() {
		// scrolling nav
		$('body').themifyScroll({
			speed : 1000
		});

		// Header Video
		if ( ! is_touch_device() ) {
			if ( typeof $.BigVideo !== 'undefined' ) {
				var $videos = $('[data-fullwidthvideo]'),
					fwvideos = [];
				$.each($videos, function (i, elm) {
					fwvideos[i] = new $.BigVideo({
						useFlashForFirefox: true,
						container: $(elm).find('#header'),
						id: i
					});
					fwvideos[i].init();
					fwvideos[i].show($(elm).data('fullwidthvideo'), {
						doLoop : true
					});
				});
			}
		}

		// Carousel initialization //////////////////////////
		if(typeof $.fn.carouFredSel !== 'undefined') {
			createCarousel( $( '.portfolio-post .slideshow' ), 'normal' );
		}

		var target = window.location.hash.replace(/^#\!\//, "").replace('#','');
		if (target !== '' && $('#' + target).length > 0) {
			var anchor = $('#' + target), destination = anchor.offset().top;
			themeScrollTo( destination, 1000 );
		}

		// Check if isotope is enabled ////////////////
		if ( typeof ($.fn.infinitescroll) !== 'undefined') {

			if ($('.post').length > 0) {
				// isotope container, isotope item, item fetched by infinite scroll, infinite scroll
				infiniteIsotope('#loops-wrapper', '.post', '#content .post', '#loops-wrapper', false, '');
			}

			if ($('.portfolio-post').length > 0) {
				// isotope container, isotope item, item fetched by infinite scroll, infinite scroll
				infiniteIsotope('.portfolio-wrapper', '.portfolio-post', '.portfolio-post', '.portfolio-wrapper', false, '');
			}

		}
	});

}(jQuery));