
var DefaultCtrl = {
	    scrollTop:function(){
	    	$(window.parent.document).scrollTop(0);
	    },
	    initEditable:function(){
	    	$.fn.editable.defaults.mode = 'pop';
			$('.editable').editable();
			$('.currency_editable').editable({
				source: [
	              {value: 'USD', text: 'USD'},
	              {value: 'TWD', text: 'TWD'},
	              {value: 'RMB', text: 'RMB'}
           		]
			});
			$('.category_editable').editable({
				source: [
	              {value: 'TODO', text: 'TODO'},
	              {value: 'RUNNING', text: 'RUNNING'},
	              {value: 'REVIEWING', text: 'REVIEWING'},
	              {value: 'DONE', text: 'DONE'}
           		]
			});
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
	    			case 'deleteRecord':
	    				var msg = $('#DeleteProjectMsg'+cmdVal).val();
	    				$event.preventDefault();
	    				$.confirm(msg, 'Confirm', function(){
	    					$('form#DeleteProjectActionForm'+cmdVal).ajaxSubmit({
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
	    				}, 'ui-state-error', 'ui-icon ui-icon-alert');
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
			    },
			    initTaskUis = function(){
			    	$('form.project-form input.staff-search, form.task-form input.staff-search').autocomplete({
						minLength: 1,
						source: function( request, response ) {
					      $.getJSON( $('#StaffSearchUrl').val(), {
					        term: request.term 
					      }, function(data){
					      	response( $.map( data, function( item ) {
					          return {
					            label: item.Name + '(' + item.Nickname + ')',
					            value: item.Nickname,
					            id: item.Id
					          }
					        }));
					      } );
					    },
						select: function(event, ui){
							$(event.target).next('input.charge_ids').val(ui.item.id);
							return false;
						}
					});
					$('.date-field').datepicker({dateFormat:'yy-mm-dd', minDate: new Date()});
			    };


			DefaultCtrl.scrollTop();

			$( '#CreateTaskModal' ).dialog({
		      autoOpen: false,
		      height: 270,
		      width: 500,
		      modal: true,
		      open:function(){
		      	initTaskUis();
		      },
		      buttons: {
		      	Save : function(){
		      		$('form#TaskModalForm').ajaxSubmit({
						success: function(response, statusText, xhr, $form){
							$( '#CreateTaskModal' ).dialog( "close" );		
							try{
								response = $.parseJSON(response);
								if(response.rlt){
									$.ajax({
										url:$('#UpdateTaskListUrl').val(),
										data:{
											project_id : $('input[name=project_id]').val()
										},
										type:'post',
										success: function(result){
											$('#TaskList').html(result);
											DefaultCtrl.initEditable();
										}
									});
								}else{
								}
							}catch(exp){
								console.error(exp);
							}
						}
					});
		      	},
		      	Cancel : function() {
          			$( this ).dialog( "close" );
        		}
		      }
		    });

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
			  codemirror: { // codemirror options
    			theme: 'monokai'
  			  },
  			  onImageUpload: function(files, editor, welEditable) {
    			//console.log('image upload:', files, editor, welEditable);
    			var data = new FormData(),
    			    imgUploadUrl = $('#ImgUploadUrl').val();
	            data.append('file', files[0]);
	            $.ajax({
	                data: data,
	                type: 'POST',
	                url: imgUploadUrl,
	                cache: false,
	                contentType: false,
	                processData: false,
	                success: function(imgUrl) {
	                    editor.insertImage(welEditable, imgUrl);
	                }
	            });//eo ajax
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
					 		showSteps(11);
					 	}else{
					 		$(target).next('.help-block').html(result.msg).addClass('text-danger');
					 	}
					 });//eo done()
				}

				$event.preventDefault();
			});//eo form.project-form::input[name=title]

			//change event listener for form.project-form::select.menu-combo
			$('form.project-form').on('change', 'select.menu-combo', function($event){
				var typeId = parseInt($('#TypeId').val(), 10),
				    getCategoriesByDeptIdUrl = $('#GetCategoriesByDeptIdUrl').val(),
				    departmentIds = [];

				$('select[name^=department]').each(function(idx, ele){
		 			departmentIds.push($(ele).val());
		 		});
		 		
				$.ajax({ url:getCategoriesByDeptIdUrl, data:{deptIds:departmentIds} })
		 		 .done(function(result){
		 		 	$('div.business-items, div.step-3').removeClass('hidden');
		 		 	$('div.category-list').html(result);
		 		 });//eo done()
			});//eo select.menu-combo change event listener

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

			//keyup event listener for form.project-form::select#TaskNo
			//generate task input fields
			$('form.project-form').on('change', 'select#TaskNo', function($event){
				var taskNo = parseInt($(this).val(), 10),
				    url = $('#GenerateTaskTableUrl').val();

				$event.preventDefault();

				$.ajax({ 
					data: { taskNo:taskNo },
					url: url,
					success: function(result){
						$('#TaskTableControl').removeClass('hidden').find('#TaskTableContent').html(result);
						initTaskUis();
					} 
				});

			});//eo form.project-form::select#TaskNo

			$('form.project-form').on('click', 'button.btn, a.function-control', function($event){
				var cmd = $(this).attr('cmd'),
	    		    cmdVal = $(this).attr('cmdVal'),
	    		    target = $event.target,
	    		    url = $('#GenerateTaskTableUrl').val();

	    		switch(cmd){
	    			case 'addTask':
	    				$.ajax({ url:url, data: { taskNo:1, withHeader:false }, success: function(result){$(target).parents('tr').after(result);initTaskUis();} });
	    				$event.preventDefault();
	    				break;
	    			case 'deleteTask':
	    				$(target).parents('tr').remove();
	    				$event.preventDefault();
	    				break;
	    			case 'submit':
	    				//$event.preventDefault();
	    				return;
	    			case 'uploadFile':
	    				$('input#TaskFile'+cmdVal).trigger('click').on('change', function(){
	    					var files = event.target.files,
	    					    data = new FormData(),
	    					    fileUploadUrl = $('#TaskFileUploadUrl').val();
	    					$.each(files, function(key, value){
								data.append(key, value);
							});
							data.append('id', cmdVal);
	    					$.ajax({
								data: data,
				                type: 'POST',
				                url: fileUploadUrl,
				                cache: false,
				                contentType: false,
				                processData: false,
				                dataType:'json',
						        success : function(data, textStatus, jqXHR){
						        	$('#DownloadFile'+cmdVal).attr('src', data.result)
						        }
							});
	    				});
	    				$event.preventDefault();
	    				break;
	    			case 'addTaskForReal':
	    				$('#CreateTaskModal').dialog('open');
	    				break;
	    			case 'deleteTaskForReal':
	    				var msg = $('#DeleteTaskMsg'+cmdVal).val();
	    				$.confirm(msg, 'Confirm', function(){

	    					$('#DeleteTaskForm'+cmdVal).ajaxSubmit({
								url: $('#DeleteTaskUrl').val(),
								success: function(response, statusText, xhr, $form){
									try{
										response = $.parseJSON(response);
										if(response.rlt){
											$.ajax({
												url:$('#UpdateTaskListUrl').val(),
												data:{
													project_id : $('input[name=project_id]').val()
												},
												type:'post',
												success: function(result){
													$('#TaskList').html(result);
												}
											});
										}else{
										}
									}catch(exp){
										console.error(exp);
									}
								}
							});
	    					
	    				}, 'ui-state-error', 'ui-icon ui-icon-alert');
	    				break;
	    			case 'save_project':
	    				$.ajax({
	    					url: $('#UpdateProjectUrl').val(),
	    					type: 'post',
	    					dataType: 'json',
	    					data:{
	    						pk:cmdVal,
	    						data:[
	    							{ field:'acceptance', value:$('#ProjectAcceptance').code() },
	    							{ field:'note', value:$('#ProjectNote').code() }
	    						]
	    					},
	    					success:function(result){
	    						console.info(result);
	    					}
	    				});
	    				break;
	    			case 'commit_project':
	    				var msg = $('#CommitConfirmMsg').val();
	    				$.confirm(msg, 'Confirm', function(){
	    					$('#CommitForm').ajaxSubmit({
	    						dataType:'json',
	    						success: function(result){
	    							if(result.rlt){
	    								$('#AuditMsg').html(result.msg);
	    								$(target).remove();
	    							}
	    						}
	    					});
	    				}, 'ui-state-error', 'ui-icon ui-icon-alert');//eo confirm()
	    				break;
	    		}
			});

			$('form.project-form input.staff-search').autocomplete({
				minLength: 1,
				source: function( request, response ) {
			      $.getJSON( $('#StaffSearchUrl').val(), {
			        term: request.term 
			      }, function(data){
			      	response( $.map( data, function( item ) {
		              return {
		                label: item.Name + '(' + item.Nickname + ')',
		                value: item.Nickname,
		                id: item.Id
		              }
		            }));
			      } );
			    },
				select: function(event, ui){
					var ids = $('#VerifierIds').val() === "" ? [] : $('#VerifierIds').val().split(','),
					    names = $('#VerifierNames').val() === "" ? [] : $('#VerifierNames').val().split(',');
					if(ids.indexOf(ui.item.id) === -1){
						ids.push(ui.item.id);
						names.push(ui.item.value);
						$('#ProjectVerifiers').after('<span class="badge">'+ui.item.label+'</span>');	
					}
					names = names.join(',') ;
					$('#VerifierIds').val(ids);
					$('#VerifierNames').val(names);
					$('#ProjectVerifiers').val("");

					return false;
				}
			});

			//Prevent form being subbmited
			$('form.project-form').submit(function($event){

				var ajaxFormUrl = $('#ProjectAddUrl').val(),
				    value = '';

				$event.preventDefault();

				$('#SubmitBtn').attr('disabled', 'disabled');

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
							//console.error(exp);
						}
					}
				});
			});//eo form.submit()			

		},//eo actionAdd

		actionEdit:function(){
			DefaultCtrl.actionAdd();
			DefaultCtrl.initEditable();
		}

	}, //eo DefaultCtrl

	controller = $('#Controller').val(),
	action = $('#Action').val(),

	//Export Controller
	ctrl = DefaultCtrl;

if(controller !== null && action !== null){
	ctrl[action]();
}