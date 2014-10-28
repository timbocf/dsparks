/**
 * Creates the section slider functionality
 */
(function($) {

	function highlight( items ) {
		items.addClass('current-slide');
	}
	function unhighlight( gallery ) {
		$('li', gallery).removeClass('current-slide');
	}
	function changeImage( items, backObject ){
		var bgImage = items.filter(':first').attr('data-bg');
		if(backObject != '') {
			backObject.backstretch(bgImage);
		} else {
			$.backstretch(bgImage);
		}
	}

	$(window).load(function() {
	
		/////////////////////////////////////////////
		// Section slider
		/////////////////////////////////////////////
		if($('.slider-section').length > 0) {
			$('.slider-section').each( function(index) {
				var sectionSlider = this;
				var itemIndex = ($('li', sectionSlider).length > 5)? '0': '1';
				var backObject = $(sectionSlider).closest('section');
				$('.slides', sectionSlider).carouFredSel({
					responsive: true,
					circular: themifySectionVars.wrap,
					infinite: themifySectionVars.wrap,
					prev: {
						button: $('.carousel-prev', sectionSlider),
						key: 'left',
						onBefore: function(items) {
							var newItems = items.items.visible;
							unhighlight( sectionSlider );
							changeImage( newItems, backObject );
						},
						onAfter	: function(items) {
							var newItems = items.items.visible;
							highlight( newItems.filter(':eq(0)') );
						}
					},
					next: {
						button: $('.carousel-next', sectionSlider),
						key: 'right',
						onBefore: function(items) {
							var newItems = items.items.visible;
							unhighlight( sectionSlider );
							changeImage( newItems, backObject );
						},
						onAfter	: function(items) {
							var newItems = items.items.visible;
							highlight( newItems.filter(':eq('+itemIndex+')') );
						}
					},
					width: '100%',
					auto: {
						play : themifySectionVars.play,
						pauseDuration: themifySectionVars.autoplay,
						button: $('.carousel-playback', sectionSlider)
					},
					swipe: true,
					scroll: {
						items: 1,
						duration: themifySectionVars.speed,
						onBefore: function(items) {
							var newItems = items.items.visible;
							unhighlight( sectionSlider );
							changeImage( newItems, backObject );
						},
						onAfter	: function(items) {
							var newItems = items.items.visible;
							highlight( newItems.filter(':eq('+itemIndex+')') );
						}
					},
					items: {
						visible: 5,
						minimum: 1,
						width: 20
					},
					onCreate : function (){
						$(sectionSlider).css( {
							'height': 'auto',
							'visibility' : 'visible'
						});

						$('.carousel-next, .carousel-prev', sectionSlider).wrap('<div class="carousel-arrow"/>');
						$('.caroufredsel_wrapper + .carousel-nav-wrap', sectionSlider).remove();

						$('li:first', sectionSlider).addClass('current-slide');

						if($('li', sectionSlider).length > 2){
							$('.carousel-playback', sectionSlider).css('display', 'inline-block');
						}

						if(!themifySectionVars.play) {
							$('.carousel-playback', sectionSlider).hide();
						}
					}
				}).find("li").click( function() {
					unhighlight( sectionSlider );
					highlight( $(this) );
					$('li', sectionSlider).trigger("slideTo", [
						$(this),
						0,
						false,
						{
							items: 1,
							duration: 300,
							onBefore: function(items) { },
							onAfter	: function(items) { }
						},
						null,
						'next']);

					// Set image and index using current data properties
					changeImage( $(this), backObject );
				}).css("cursor", "pointer");

				/////////////////////////////////////////////
				// Initialize section slider background
				/////////////////////////////////////////////
				
				var themifyImages = [];

				// Initialize images array with URLs
				$('li', sectionSlider).each(function(){
					themifyImages.push( $(this).attr('data-bg') );
				});

				$(themifyImages).each(function() {
					$("<img/>").attr('src', this);
				});

				// Call backstretch for the first time
				backObject.backstretch(themifyImages[0], {
					fade : themifySectionVars.speed
				});
			}); // end each .slider-section
		} // end if .slider-section
	});
	
	$(document).ready(function() {

		/////////////////////////////////////////////
		// Parse injected vars
		/////////////////////////////////////////////
		themifySectionVars.autoplay = parseInt(themifySectionVars.autoplay, 10);
		if ( themifySectionVars.autoplay <= 10 ) {
			themifySectionVars.autoplay *= 1000;
		}
		themifySectionVars.speed = parseInt(themifySectionVars.speed, 10);
		themifySectionVars.play = themifySectionVars.play != 'no';
		themifySectionVars.wrap = themifySectionVars.wrap != 'no';

		////////////////////////
		// Add wrap for styling
		////////////////////////
		$('.slider-section img').each(function() {
			$(this).wrap('<span class="image-wrap" style="width: auto; height: auto;"/>');
			$(this).removeAttr('class');
		});

		/////////////////////////////////////////////
		// Pause carousel
		/////////////////////////////////////////////
		$('.carousel-playback').click(function(){
			$(this).toggleClass('paused');
		});
	
		// Adjust slider on window resizing stop
		$(window).on('resize', function () {
			if($('.slider-section').length > 0) {
				$('.slider-section').closest('section').each( function() {
					var sectionSlider = $(this).data('backstretch');
					if(sectionSlider)
						sectionSlider.resize();
				});
			}
		});
	});

}(jQuery));