
$(document).ready(function(){

	$('.collapse').collapse();
	//pagination
	$('ul.pagination a').click(function($event){
		var cmdVal = $(this).attr('cmdVal'),
			targetForm = $(this).attr('target');
		
		$event.preventDefault();
		$('#'+targetForm).find('input[name=page]').val(cmdVal).end().submit();
	});
	//date-pick fields
	$('.date-field').datepicker({dateFormat:'yy-mm-dd'});
});