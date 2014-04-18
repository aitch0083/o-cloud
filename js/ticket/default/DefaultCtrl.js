
var DefaultCtrl = {
	    scrollTop:function(){
	    	$(window.parent.document).scrollTop(0);
	    },
		actionIndex:function(){
			var gridTableTarget = '#GridTable',
			    gridTableUrl = $('#GridTableUrl').val(),
			    colNames = ["Inv No", "Date", "Amount", "Tax", "Total", "Notes"],
			    colModel = [
		            { name: "invid", width: 55 },
		            { name: "invdate", width: 90 },
		            { name: "amount", width: 80, align: "right" },
		            { name: "tax", width: 80, align: "right" },
		            { name: "total", width: 80, align: "right" },
		            { name: "note", width: 150, sortable: false }
		        ],
		        pager = $('#GridTablePager'),
		        rowNum = $('#GridTableRowNum').val(),
		        sortname = $('#GridTableSortname').val(),
		        sortorder = $('#GridTableSortorder').val(),
		        caption = $('#GridTableCaption').val();

		    DefaultCtrl.scrollTop();

			//Register event listeners
	    	$('.btn').on('click', function($event){
	    		var cmd = $(this).attr('cmd'),
	    		    cmdVal = $(this).attr('cmdVal'),
	    		    target = $(this).attr('target');

	    		switch(cmd){
	    			case 'addRecord':
	    			case 'filterRecord':
	    				$event.preventDefault();
	    				$(parent.document).find(target).attr('src', cmdVal);
	    				break;
	    			case 'accept_prj':
	    				var msg = $('#AcceptProjectMsg'+cmdVal).val();
	    				$event.preventDefault();
	    				$.confirm(msg, 'Confirm', function(){
	    					$('#EditProjectActionForm'+cmdVal).submit();
	    				}, 'ui-state-highlight', 'ui-icon ui-icon-info');
	    				break;
	    			case 'decline_prj':
	    				var msg = $('#DeclineProjectMsg'+cmdVal).val();
	    				$event.preventDefault();
	    				$.confirm(msg, 'Confirm', function(){
	    					$('#DeclineProjectActionForm'+cmdVal).ajaxSubmit({
	    						success: function(response, statusText, xhr, $form){
	    							try{
	    								response = $.parseJSON(response);
	    								if(response.rlt){
	    									$.showDialog(response.msg, null, null, function(){
	    										$('tr.target_rec'+cmdVal).remove();
											});
	    								}else{
	    									$.showDialog(response.msg);
	    								}
	    							}catch(exp){
	    								console.error(exp);
	    							}
	    						}
	    					});
	    				}, 'ui-state-error', 'ui-icon ui-icon-alert');//eo confirm()
	    				break;
	    			case 'editRecord':
	    				$('form#EditProjectActionForm'+cmdVal).submit();
	    				break;
	    		}
	    	});
		},//eo actionIndex

		actionAdd:function(){

			var initForm = function(resetForm){
					for(idx = 11 ; idx > 1 ; idx -= 1){
						$('div.step-'+idx).addClass('hidden');
					}
					$('span.contact-label').remove();
					if(resetForm){
						$('form.project-form')[0].reset();
					}
			    }, 
			    showSteps = function(step){
			    	for(i = 4 ; i <= step ; i+=1){ //unlock step-4 ~ step-11
						$('div.step-'+i).removeClass('hidden');
					}
			    	$.doAdjHeight();
			    };

			DefaultCtrl.scrollTop();

			//date-pick fields
			$('.date-field').datepicker({dateFormat:'yy-mm-dd', minDate: new Date()});

			//Register ajax-loading effect
			$(document).ajaxStart(function(){
				$('form.ajax-form').addClass('loading-jar-bg');
			}).ajaxComplete(function(){
				$.doAdjHeight();
				$('form.ajax-form').removeClass('loading-jar-bg');
			});

			//Init text editor
			$('.summernote').summernote({
			  height: 200,
			  toolbar: [
			    //['style', ['style']], // no style button
			    ['style', ['bold', 'italic', 'underline', 'clear']],
			    ['font', ['strike']],
			    ['fontsize', ['fontsize']],
			    ['color', ['color']],
			    ['para', ['ul', 'ol', 'paragraph']],
			    ['height', ['height']],
			    //['insert', ['picture', 'link']], // no insert buttons
			    ['table', ['table']]
			    //['help', ['help']] //no help button
			  ],
			  codemirror: { // codemirror options
    			theme: 'monokai'
  			  }
			});
			
			//change event listener for form.project-form::select.menu-combo,
			//generate the combos
			$('form.project-form').on('change', 'select.menu-combo', function($event){
				var startLevel = parseInt($(this).attr('startLevel'), 10),
					nextLevel = startLevel + 1;
					maxLevel = $(this).attr('maxLevel'),
					getListUrl = $('#GetListUrl').val(),
					selfId = parseInt($(this).val(), 10),
					ajaxCombo = $(this).hasClass('ajax-combo'),
					generateMenuItem = function(level, target){
						$.ajax({ url:getListUrl, data:{startLevel:nextLevel, parentId:selfId} })
						 .done(function(result){
						 	$(target).after(result);
						 });//eo done()
					};//eo generateMenuItem()

				if(!ajaxCombo){
					$('select').remove('.ajax-combo');
					$('span').remove('.control-label');
				}else{
					// if the current combo is the ajax combo, once it changed, 
					// remove the following child combo
					$('span').remove('.span-'+nextLevel);
					$('span').remove('.contact-label');
					$('select[startLevel='+nextLevel+']').remove('.ajax-combo');
				}

				if(nextLevel <= maxLevel && selfId > 0){//iterate to generate menu items
					generateMenuItem(nextLevel, this);
				}

				$event.preventDefault();
			});//eo form.project-form::select.menu-combo

			//change event listener for form.project-form::select.ajax-combo
			//bring the contact for each selected department 
			$('form.project-form').on('change', 'select.ajax-combo', function($event){
				var selfId = $(this).val(),
					getContactUrl = $('#GetContactUrl').val(),
					checkDeptOpenUrl = $('#CheckDeptOpenUrl').val(),
					target = this,
					idx = 0,
					getContact = function(deptIsOpen){
						$('div').remove('.is_open_msg');
						/*if(!deptIsOpen.is_open){
							$('#ProjectFormLegend').append(deptIsOpen.msg);
							initForm(false);
							return;
						}*/
						//if the deparment is open for businese, then get the contact
						$.ajax({url:getContactUrl, data:{departmentId:selfId}, dataType:'json'})
						 .done(function(result){
						 	if(result !== false && result.Name !== undefined){//has contact
						 		var msg = 'contact:' + result.Name + result.title + (result.ExtNo === '' ? '' : '(Ext:'+result.ExtNo+')');
						 		$(target).after($.showMsg(msg, 'glyphicon glyphicon-user contact-label'));
						 		$('#ContactId').val(result.Id);
						 	}
						 	//init step-2
						 	var allset = true;
						 	$('div.step-1').find('select').each(function(idx, ele){
						 		if($(ele).val() === '0'){ 
						 			allset = false;	
						 			return allset;//return false to break
						 		}
						 	});
						 	if(allset){
						 		$('div.step-2').removeClass('hidden');
						 	}else{
						 		$('div.step-2').addClass('hidden');
						 	}
						 });
					};
				//check department is open for businese
				$.ajax({url:checkDeptOpenUrl, data:{departmentId:selfId}, dataType:'json' }).done(getContact);					

			});//eo form.project-form::select.ajax-combo

			//keyup event listener for form.project-form::input[name=title]
			//check duplication
			$('form.project-form').on('keyup', 'input[name=title]', function($event){

				var minlength = $(this).attr('minlength'),
				    maxlength = $(this).attr('maxlength'),
				    value = $(this).val(),
				    target = this,
				    checkDupUrl = $('#CheckProjectNameDupUrl').val(),
				    getCategoriesByDeptIdUrl = $('#GetCategoriesByDeptIdUrl').val(),
				    departmentIds = [],
				    typeId = $('#ProjectTypes').val();

				if(value.length >= minlength){
					$(this).parents('div').find('help-block').removeClass('alert alert-danger').html();
					$.ajax({ url:checkDupUrl, data:{key:value, fields:'title'}, dataType:'json' })
					 .done(function(result){
					 	if(!result.is_dup){
					 		$(target).next('.help-block').html(result.msg).addClass('text-success');
					 		//init step-3, load project categories
					 		$('select[name^=department]').each(function(idx, ele){
					 			departmentIds.push($(ele).val());
					 		});
					 		$('div.step-3').removeClass('hidden');
					 	}else{
					 		$(target).next('.help-block').html(result.msg).addClass('text-danger');
					 	}
					 });//eo done()
				}

				$event.preventDefault();
			});//eo form.project-form::input[name=title]

			$('form.project-form').on('change', 'select#ProjectTypes', function($event){
				var typeId = parseInt($('#ProjectTypes').val(), 10),
				    getCategoriesByDeptIdUrl = $('#GetCategoriesByDeptIdUrl').val(),
				    departmentIds = [];

				$('select[name^=department]').each(function(idx, ele){
		 			departmentIds.push($(ele).val());
		 		});

				$.ajax({ url:getCategoriesByDeptIdUrl, data:{deptIds:departmentIds} })
		 		 .done(function(result){
		 		 	if(typeId !== 2){//TODO : Fix this magic number
		 		 		$('div.business-items').addClass('hidden');
		 		 		$('div.category-list').html('<input type="hidden" name="category_id" value="0" />');	
		 		 		showSteps(11);
						return;
					}
		 		 	$('div.business-items').removeClass('hidden');
		 		 	$('div.category-list').html(result);
		 		 });//eo done()
			});

			//keyup event listener for form.project-form::select[name=category_id]
			//init other steps
			$('form.project-form').on('change', 'select[name=category_id]', function($event){
				var val = parseInt($(this).val(), 10);

				$event.preventDefault();

				if(val === 0){
					return;
				}
				showSteps(11);
			});//eo form.project-form::select[name=category_id]

			//Prevent form being subbmited
			$('form.project-form').submit(function($event){

				var ajaxFormUrl = $('#ProjectAddUrl').val(),
				    value = '';

				$event.preventDefault();
				$(this).ajaxSubmit({
					url: ajaxFormUrl,
					beforeSubmit: function(){
						$('div.has-error').removeClass('has-error');
						//check form
						$('input[required], textarea[required]').each(function(idx, ele){
							value = $.trim($(this).val());
							if(value === ''){
								$(this).focus().parents('div.form-group').addClass('has-error');
								return false;
							}
						});
						return true;
					},
					success: function(response, statusText, xhr, $form){
						try{
							response = $.parseJSON(response);
							DefaultCtrl.scrollTop();
							if(response.result){
								initForm();
								$.showDialog(response.msg, null, null, function(){
									self.location = response.redirect;
								});
							}else{
							}
						}catch(exp){
							console.error(exp);
						}
					}
				});
			});			

		},//eo actionAdd

		actionEdit:function(){
			DefaultCtrl.actionAdd();
		}

	}, //eo DefaultCtrl

	controller = $('#Controller').val(),
	action = $('#Action').val(),

	//Export Controller
	ctrl = DefaultCtrl;

if(controller !== null && action !== null){
	ctrl[action]();
}