/** ========================================================================
*     Widget
* ------------------------------------------------------------------------
*/
(function($, window, document, undefined){
$(document).ready(function(){

	var input = $('#set_' + widget_img_choose.meta_key);

	$('a', '#img-choose').click( function(e){
		e.preventDefault();

		$('a', '#img-choose').removeClass('selected');

		var val = $(this).data('value');
		$(this).addClass('selected');
		input.val(val);
	});

});
})(jQuery, window, document);