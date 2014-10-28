/**
 * Javascript code for WP Admin screen
 * By http://www.themify.me/
 */

jQuery(document).ready(function($){
	var $sub = $('#themify-subtitle-group');
	if($sub.length > 0) {
		$sub.contents().appendTo($('#titlewrap'));
	}
	var themifytitle = $('#themify_subtitle'), themifytitleprompt = $('#subtitle-prompt-text');
	if ( themifytitle.val() == '' ) {
		themifytitleprompt.removeClass('screen-reader-text');
	}
	themifytitleprompt.click(function(){
		$(this).addClass('screen-reader-text');
		themifytitle.focus();
	});
	themifytitle.blur(function(){
		if ( this.value == '' ) {
			themifytitleprompt.removeClass('screen-reader-text');
		}
	}).focus(function(){
			themifytitleprompt.addClass('screen-reader-text');
		}).keydown(function(e){
			themifytitleprompt.addClass('screen-reader-text');
			$(this).unbind(e);
		});
});