var DepartmentCtrl = {
		init: function(){
			//date-pick fields
			$('.date-field').datepicker({dateFormat:'yy-mm-dd', minDate: new Date()});
			$('.editable').editable();
			$('#AssignLeaderDialog').dialog({
				autoOpen: false,
				title:'Assign Leader',
			    height: 160,
			    width: 185,
			    modal: true,
				buttons:{
					'Save':DepartmentCtrl.actionUpdateLeaderContact,
					'Cancel':function($event){
						$(this).dialog('close');
					}
				}
			});

			//Register event listeners
	    	$('.btn').on('click', function($event){
	    		var cmd = $(this).attr('cmd'),
	    		    cmdVal = $(this).attr('cmdVal'),
	    		    target = $(this).attr('target');

	    		$event.preventDefault();

	    		switch(cmd){
	    			case 'assign_leader':
	    			case 'assign_contact':
	    				$('#TargetUser').val(cmdVal);
	    				$('#TargetOperation').val(cmd);
	    				$('#AssignLeaderDialog').dialog('open');
	    				break;
	    			case 'remove_leader':
	    			case 'remove_contact':
	    				$('#Target').val(target);
	    				$('#TargetUser').val(cmdVal);
	    				$('#TargetOperation').val(cmd);
	    				DepartmentCtrl.actionUpdateLeaderContact();
	    				break;
	    		}//eo switch()
	    	});//eo .btn event listener
		},
	    actionIndex:function(){
			DepartmentCtrl.init();
		},//eo actionIndex

		actionAdd:function(){
			DepartmentCtrl.init();
		},//eo actionAdd

		actionEdit:function(){
			DepartmentCtrl.init();
		},

		actionUpdateLeaderContact: function($event){
			var targetUser = $('#TargetUser').val(),
				assignLeaderUrl = $('#AssignLeaderUrl').val(),
				departmentId = $('#LeaderCombo').val(),
				operation = $('#TargetOperation').val(),
				target = $('#Target').val();
			$.ajax({
				url: assignLeaderUrl,
				data:{
					department_id: departmentId,
					user_id: targetUser,
					operation: operation,
					target: target
				},
				type:'post',
				dataType:'json',
				success: function(result){
					$('#AssignUserRlt').html(result.msg+'<br/> <em>Refreshing...</em>');
					location.reload();
				}
			});

		}

	}, //eo DepartmentCtrl

	controller = $('#Controller').val(),
	action = $('#Action').val(),

	//Export Controller
	ctrl = DepartmentCtrl;

if(controller !== null && action !== null){
	ctrl[action]();
}