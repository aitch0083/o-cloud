jQuery.showMsg = function(msg, glyphon, className, htmlType){
	htmlType = htmlType || '<span>';
	className = className || 'control-label pull-left';
	glyphon = glyphon || '';

	return jQuery(htmlType).html(msg).addClass(glyphon+' '+className);
};
jQuery.showDialog = function(msg, title, shownCB, hiddenCB){
	jQuery('#ModalContent').html(msg);
	if(title !== undefined && title !== null){
		jQuery('#ModalTitle').html(title);
	}
	if(shownCB !== undefined && shownCB !== null){
		$('#SystemDialog').on('shown.bs.modal', shownCB);
	}
	if(hiddenCB !== undefined && hiddenCB !== null){
		$('#SystemDialog').on('hidden.bs.modal', hiddenCB);
	}
	$('#SystemDialog').modal('show');
};
jQuery.doAdjHeight = function DoAdjHeight() {
    var theFrame = $("iframe", parent.document.body);
    theFrame.each(function () {
        $(this).height($(document.body).height() + 35);
    });
};
jQuery.confirm = function(msg, title, yesCB, highlightClass, iconClass){
	highlightClass = highlightClass || 'ui-state-highlight';
	iconClass = iconClass || 'ui-icon ui-icon-info';
	$('#ConfirmDialogContent').html('<span class="'+iconClass+'" style="display:inline-block"></span> '+msg).addClass(highlightClass);
	$('#ConfirmDialog').dialog({
		title:title, 
		buttons:{
			'Yes':function(){
				yesCB();
				$(this).dialog('close');
			},
			'No':function(){
				$(this).dialog('close');
			}
		}
	});
	$('#ConfirmDialog').dialog('open');
};

$(document).ready(function(){

	var keywords = $('input[name=keywords]').val();

	$('.collapse').collapse();
	//pagination
	$('ul.pagination a').click(function($event){
		var cmdVal = $(this).attr('cmdVal'),
			targetForm = $(this).attr('target');
		
		$event.preventDefault();
		$('#'+targetForm).find('input[name=page]').val(cmdVal).end().submit();

		return false;
	});
	//date-pick fields
	$('.date-field').datepicker({dateFormat:'yy-mm-dd'});

	//highlight keyword search
	if(keywords !== null && keywords !== undefined && keywords !== ''){
		$('body').highlight(keywords, {element:'em', className:'highlight-bg'});
	}

	$('a.menuBtn').click(function($event){
		
		var cmdVal = $(this).attr('cmdVal'),
		    cmd = $(this).attr('cmd'),
		    target = $event.target;

		$event.preventDefault();

		switch(cmd){
			case 'changeWorkspace':
				$('iframe#workspace').attr('src', cmdVal);
				break;
		}
	});

	$('#side-menu').metisMenu({toggle: false});

	$('.tipinfos').tooltip({container:'body'})

	$('#ConfirmDialog').dialog({autoOpen:false, width: 300, modal: true});

    $(function () {
        $.doAdjHeight();
        $(window).resize(function () {
            $.doAdjHeight();
        });
    });

});