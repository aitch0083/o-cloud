<?php
/* @var $this DefaultController
* All the variables are defined in the DefaultController
*/
$recId = $project['id'];
$projectType = '';
?>

<form class="form-horizontal project-form ajax-form" role="form" method="post" action="<?php echo $editAction; ?>">
	
	<?php $this->widget('widgets.BreadCrumbs', array('items'=>$crumbs)); ?>
	
	<legend id="ProjectFormLegend"><?php Utils::icon('plus'); Utils::e('Edit Project::'.$project['title']); ?></legend>
	<!-- ACCESS TOKEN -->
	<input type="hidden" id="AccessToken" name="access_token" value="<?php echo $accessToken; ?>" />
	<!-- CONTACT -->
	<input type="hidden" id="ContactId" name="contact_id" value="<?php echo $project['contact_id']; ?>" />
	<!-- IS_PUBLISHED -->
	<input type="hidden" id="IsPublished" name="is_published" value="<?php echo $project['is_published']; ?>" />
	<!-- IS_DONE -->
	<input type="hidden" id="IsDone" name="is_done" value="<?php echo $project['is_done']; ?>" />
	<!-- REWARDS -->
	<input type="hidden" id="Rewards" name="rewards" value="<?php echo $project['rewards']; ?>" />
	<!-- DEPARTMENT -->
	<input type="hidden" id="DepartmentId" name="department_id" value="<?php echo $project['department_id']; ?>" />

	<div class="form-group step-1">
	    <label for="DepartmentCombo" class="col-sm-2 control-label"><?php Utils::e('Department'); ?>:</label>
	    <div class="col-sm-10">
	    	<ul class="breadcrumb">
	    		<?php Utils::printTree($departments, $deptPath); ?>
	    		<li><?php Utils::e('Conatct: {name}', true, array('name'=>$contact['Name'])); ?></li>
	    	</ul>
	    </div>
	</div>
	<?php if($project['belongs_to_me']): ?>
	<div class="form-group step-1">
	    <label for="SelfPromotion" class="col-sm-2 control-label"><?php Utils::e('Assign To Me'); ?>:</label>
	    <div class="col-sm-10">
	        <?php echo $project['belongs_to_me'] ? Utils::e('My project') : ''; ?>
	    </div>
	</div>
	<?php endif; ?>
	<div class="form-group step-2">
	    <label for="ProjectName" class="col-sm-2 control-label"><?php Utils::e('Project Name'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectName" type="text" class="form-control disabled" name="title" maxlength="100" minlength="5" placeholder="name your project..." value="<?php echo $project['title']; ?>" disabled required/>
	      <span class="help-block"><?php Utils::e('Project name cannot duplicate with others! Max: 100 chars; Min: 5 chars.'); ?></span>
	    </div>
	    <label for="EstimatedProfit" class="col-sm-2 control-label"><?php Utils::e('Estimated Profit'); ?>:</label>
	    <div class="col-sm-2">
	    	<input id="EstimatedProfit" type="number" class="form-control disabled" name="estimated_profit" maxlength="10" minlength="5" placeholder="money here..." value="<?php echo $project['estimated_profit']; ?>" disabled required/>
	    </div>
	    <div class="col-sm-5">
	    	<select name="currency_type" class="form-control">
	    		<option value="USD" <?php echo $project['currency_type'] === 'USD' ? 'selected' : '' ?>>USD</option>
	    		<option value="TWD" <?php echo $project['currency_type'] === 'TWD' ? 'selected' : '' ?>>TWD</option>
	    		<option value="RMB" <?php echo $project['currency_type'] === 'RMB' ? 'selected' : '' ?>>RMB</option>
	    	</select>
	    </div>
	    <div class="col-sm-10 col-sm-offset-2">
	    	<span class="help-block"><?php Utils::e('How much profit you think this project can produce?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-3">
		<label for="ProjectType" class="col-sm-2 control-label"><?php Utils::e('Project Type'); ?>:</label>
	    <div class="col-sm-10">
	    	<div class="type-list">
	    		<select name="type_id" id="ProjectTypes" class="form-control disabled" disabled>
	    			<option value="0"><?php echo Utils::e('All...'); ?></option>
	    			<?php foreach($projectTypes as $idx=>$type): ?>
	    			<?php if($project['type_id'] == $type['id']){ $projectType = strtolower($type['name']); }  ?>
	    			<option <?php echo $project['type_id'] == $type['id'] ? 'selected' : ''; ?> value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
	    			<?php endforeach; ?>
	    		</select>
	    	</div>
	    	<span class="help-block"><?php Utils::e('Different types have different audit processes.'); ?></span>
	    </div>
	    <?php if($project['type_id'] === 2): ?>
	    <div class="business-items">
		    <label for="ProjectCategory" class="col-sm-2 control-label"><?php Utils::e('Business Items'); ?>:</label>
		    <div class="col-sm-10">
		    	<div class="category-list">
		    		<select name="category_id" id="ProjectTypes" class="form-control disabled" disabled>
		    			<option value="0"><?php echo Utils::e('All...'); ?></option>
		    			<?php foreach($projectCategories as $idx=>$category): ?>
		    			<option <?php echo $project['category_id'] === $category['id'] ? 'selected' : ''; ?> value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
		    			<?php endforeach; ?>
	    			</select>
		    	</div>
		    	<span class="help-block"><?php Utils::e('Each department provides different categories.'); ?></span>
		    </div>
		</div>
		<?php endif; ?>
	</div>
	<div class="form-group step-4">
	    <label for="ProjectPurpose" class="col-sm-2 control-label"><?php Utils::e('Purpose'); ?>:</label>
	    <div class="col-sm-10">
	      <a href="#" class="editable" id="ProjectPurpose<?php echo $task['id'] ?>" data-type="textarea" data-pk="<?php echo $project['id']; ?>" data-url="<?php echo $editAction, '?type=purpose'; ?>" data-title="Edit" data-value="<?php echo $project['purpose']; ?>"><?php echo ($project['purpose']); ?></a>
	      <span class="help-block"><?php Utils::e('Describe why you need this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-5">
	    <label for="ProjectTargets" class="col-sm-2 control-label"><?php Utils::e('Targets'); ?>:</label>
	    <div class="col-sm-10">
	      <a href="#" class="editable" id="ProjectDemands<?php echo $task['id'] ?>" data-type="textarea" data-pk="<?php echo $project['id']; ?>" data-url="<?php echo $editAction, '?type=demands'; ?>" data-title="Edit" data-value="<?php echo $project['demands']; ?>"><?php echo ($project['demands']); ?></a>
	      <span class="help-block"><?php Utils::e('Who and what will benefit from this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-7">
		<label for="ProjectRange" class="col-sm-2 control-label"><?php Utils::e('Scope'); ?>:</label>
	    <div class="col-sm-10">
	      <a href="#" class="editable" id="ProjectApplyRange<?php echo $task['id'] ?>" data-type="textarea" data-pk="<?php echo $project['id']; ?>" data-url="<?php echo $editAction, '?type=apply_range'; ?>" data-title="Edit" data-value="<?php echo $project['apply_range']; ?>"><?php echo ($project['apply_range']); ?></a>
	      <span class="help-block"><?php Utils::e('How many departments will be effected by this project?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-8">
		<label for="ProjectVerifiers" class="col-sm-2 control-label"><?php Utils::e('Verifiers'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectVerifiers" type="text" class="form-control disabled" name="verifiers" maxlength="100" minlength="5" placeholder="Who can help you with verifying this project" value="<?php echo $project['verifier_names']; ?>" required disabled/>
	      <span class="help-block"><?php Utils::e('Who can help you with verifying this project'); ?></span>
	    </div>
	</div>
	<div class="form-group step-9">
		<label for="ProjectExpectingDate" class="col-sm-2 control-label"><?php Utils::e('Expecting Finish Date'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectExpectingDate" type="text" class="form-control date-field disabled" disabled name="expecting_date" maxlength="10" minlength="10" placeholder="The date you wish it could be finished." value="<?php echo $project['expecting_date']; ?>" required/>
	      <span class="help-block"><?php Utils::e('When do you need this project be done?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-6">
		<label for="ProjectAcceptance" class="col-sm-2 control-label"><?php Utils::e('Acceptance Criteria'); ?>:</label>
	    <div class="col-sm-10">
	      <textarea id="ProjectAcceptance" class="form-control summernote" name="acceptance" required>
	      	<?php echo $project['acceptance']; ?>
	      </textarea>
	      <span class="help-block"><?php Utils::e('Name the criterions for closing this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-10">
		<label for="ProjectNote" class="col-sm-2 control-label"><?php Utils::e('Note'); ?>:</label>
	    <div class="col-sm-10">
	      <textarea id="ProjectNote" class="form-control summernote" name="note" />
	      	<?php echo $project['note']; ?>
	  	  </textarea>
	  	  <span class="help-block"><?php Utils::e('Anything else you want to say?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-10">
		<label for="ProjectTask" class="col-sm-2 control-label"><?php Utils::e('Tasks'); ?>:</label>
		<div id="TaskList" class="col-sm-10">
			<?php if(count($tasks)): ?>
			<table class="table table-bordered table-condensed table-striped table-hover">
				<thead>
					<tr>
						<th><?php Utils::e('Description'); ?></th>
						<th><?php Utils::e('Deliverable'); ?></th>
						<th><?php Utils::e('Due Date'); ?></th>
						<th><?php Utils::e('Responsibles'); ?></th>
						<th><?php Utils::e('Budget'); ?></th>
						<th><?php Utils::e('Status'); ?></th>
						<th><?php Utils::e('Files'); ?></th>
						<th><?php Utils::e('Actions'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php    $budgetRMB = 0;
						 $budgetUSD = 0;
						 $budgetTWD = 0;
				         $daysLeft = 0; 
				?>	
				<?php foreach($tasks as $idx=>$task): ?>
					<?php 
						$dateDiff = date_diff(date_create($this->today), date_create($task['expecting_date']));  
						$dayDiff = intval($dateDiff->format('%a'));		
						$daysLeft += $dayDiff;
					?>
					<tr>
						<td><a href="#" class="editable" id="TaskContent<?php echo $task['id'] ?>" data-type="text" data-pk="<?php echo $task['id']; ?>" data-url="<?php echo $editTaskUrl, '?type=content'; ?>" data-title="Edit"><?php echo $task['content']; ?></a></td>
						<td><a href="#" class="editable" id="TaskDeliverable<?php echo $task['id'] ?>" data-type="text" data-pk="<?php echo $task['id']; ?>" data-url="<?php echo $editTaskUrl, '?type=deliverable'; ?>" data-title="Edit"><?php echo $task['deliverable']; ?></a></td>
						<td><?php 
								echo substr($task['expecting_date'], 0, 10); 
								if($dayDiff > 0){
							  	  echo Utils::eBadge($dayDiff.' days', true, 'badge-info');
							  	}else{
							  	  echo Utils::eBadge('DUED!!', true, 'badge-danger');
							  	}
							?></td>
						<td><?php echo $task['in_charge_names']; ?></td>
						<td width="120"><?php 
								switch($task['currency_type']){
									case 'RMB': $budgetRMB += $task['budget']; break;
									case 'USD': $budgetUSD += $task['budget']; break;
									case 'TWD': $budgetTWD += $task['budget']; break;
								}
							?>
							<a href="#" class="editable" id="TaskBudget<?php echo $task['id'] ?>" data-type="number" data-pk="<?php echo $task['id']; ?>" data-url="<?php echo $editTaskUrl, '?type=budget'; ?>" data-title="Edit" data-value="<?php echo $task['budget']; ?>"><?php echo number_format($task['budget']); ?></a>
							<a href="#" class="currency_editable" id="TaskCurrencyType<?php echo $task['id'] ?>" data-type="select" data-pk="<?php echo $task['id']; ?>" data-url="<?php echo $editTaskUrl, '?type=currency_type'; ?>" data-title="Edit" data-value="<?php echo $task['currency_type']; ?>"><?php echo $task['currency_type']; ?></a>
						</td>
						<td>
							<a href="#" class="category_editable" id="TaskCategory<?php echo $task['id'] ?>" data-type="select" data-pk="<?php echo $task['id']; ?>" data-url="<?php echo $editTaskUrl, '?type=category'; ?>" data-title="Edit" data-value="<?php echo $task['category']; ?>"><?php echo $task['category']; ?></a>
						</td>
						<td width="50">
							<?php if($task['file'] !== ''): ?>
							<a id="DownloadFile<?php echo $task['id']; ?>" class="function-control" href="<?php echo $task['file'];  ?>" target="_blank"><?php Utils::icon('download'); ?></a>
							<?php endif; ?>
							<a class="function-control" href="#" cmd="uploadFile" cmdVal="<?php echo $task['id']; ?>"><?php Utils::icon('upload'); ?></a>
							<input id="TaskFile<?php echo $task['id']; ?>" type="file" class="form-control input-sm hidden" name="task[files][]" /> 
						</td>
						<td class="text-center" width="80">
							<div class="btn-group">
								<?php if($idx >= (count($tasks) - 1) ): ?>
								<button class="btn btn-xs btn-info tipinfos newTask<?php echo $idx; ?>" cmd="addTaskForReal" cmdVal="" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Plus'); ?>" type="button"><?php Utils::icon('plus'); ?></button>
								<?php endif; ?>
								<button class="btn btn-xs btn-info tipinfos deleteTask<?php echo $idx; ?>" cmd="deleteTaskForReal" cmdVal="<?php echo $task['id']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Delete'); ?>" type="button"><?php Utils::icon('trash'); ?></button>
								<form id="DeleteTaskForm<?php echo $task['id']; ?>" class="hidden" action="<?php echo $deleteTaskUrl; ?>" method="post">
									<input type="hidden" name="project_id" value="<?php echo $this->encode($project['id']); ?>"/>
									<input type="hidden" name="task_id" value="<?php echo $this->encode($task['id']); ?>"/>
								</form>
								<input type="hidden" id="DeleteTaskMsg<?php echo $task['id']; ?>" value="<?php Utils::e('Are you sure about deleting this task?') ?>" />
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
					<tr>
						<td colspan="8" class="text-right">
							<?php echo $daysLeft  > 0 ? Utils::e('<p>Days Left: dum(days)', false, array('dum'=>$daysLeft)) : Utils::eBadge('DUED!!', true, 'badge-danger'); ?>
							<?php echo $budgetRMB > 0 ? Utils::e('<p>Budget: num(RMB)</p>', false, array('num'=>number_format($budgetRMB))) : ''; ?>
							<?php echo $budgetUSD > 0 ? Utils::e('<p>Budget: num(USD)</p>', false, array('num'=>number_format($budgetUSD))) : ''; ?>
							<?php echo $budgetTWD > 0 ? Utils::e('<p>Budget: num(TWD)</p>', false, array('num'=>number_format($budgetTWD))) : ''; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php else: ?>
			<button class="btn btn-xs btn-info tipinfos newTask<?php echo $idx; ?>" cmd="addTaskForReal" cmdVal="" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Plus'); ?>" type="button"><?php Utils::icon('plus'); ?></button>
			<?php endif; ?>
		</div>
	</div>
	<div class="form-group step-11">
	    <div class="col-sm-offset-2 col-sm-10">
	      <div id="AuditMsg" class="alert alert-info"><?php Utils::e('Auditor: name(email)', true, array('name'=>$auditor['Name'], 'email'=>$auditor['Mail'])); ?></div>
	      <div class="btn-group">
		      <button type="button" cmd="save_project" cmdVal="<?php echo $recId; ?>" class="btn btn-default"><?php Utils::e('Save'); ?></button>
		      <?php if(!$project['is_commited']): ?>
		      <button type="button" cmd="commit_project" cmdVal="<?php echo $recId; ?>" class="btn btn-default"><?php Utils::e('Commit'); ?></button>
		  <?php endif; ?>
	  	  </div>
	    </div>
  	</div>
</form>

<!-- Ajax Parameters -->
<input type="hidden" id="GetListUrl" value="<?php echo $getMenuAction; ?>"/>
<input type="hidden" id="GetContactUrl" value="<?php echo $getContactAction; ?>"/>
<input type="hidden" id="CheckDeptOpenUrl" value="<?php echo $checkDeptOpenUrl; ?>"/>
<input type="hidden" id="CheckProjectNameDupUrl" value="<?php echo $checkProjectNameDupUrl; ?>" />
<input type="hidden" id="GetCategoriesByDeptIdUrl" value="<?php echo $getCategoriesByDeptIdUrl; ?>" />
<input type="hidden" id="ProjectAddUrl" value="<?php echo $projectAddUrl; ?>" />
<input type="hidden" id="GenerateTaskTableUrl" value="<?php echo $generateTaskTableUrl; ?>" />
<input type="hidden" id="ImgUploadUrl" value="<?php echo $imgUploadUrl; ?>" />
<input type="hidden" id="StaffSearchUrl" value="<?php echo $staffSearchUrl; ?>" />
<input type="hidden" id="TaskFileUploadUrl" value="<?php echo $editTaskUrl, '?type=task_file'; ?>" />
<input type="hidden" id="UpdateTaskListUrl" value="<?php echo $updateTaskListUrl; ?>" />
<input type="hidden" id="DeleteTaskUrl" value="<?php echo $deleteTaskUrl; ?>" />
<input type="hidden" id="UpdateProjectUrl" value="<?php echo $updateProjectUrl; ?>" />
<input type="hidden" id="CommitUrl" value="<?php echo $editAction, '?type=is_commited'; ?>" />
<input type="hidden" id="CommitConfirmMsg" value="<?php Utils::e('Are you sure you want to commit? Once you commit, this project is offically running and cannot be stopped.'); ?>" />

<!-- Modal Forms -->
<div id="CreateTaskModal" title="<?php echo Utils::e('Create New Task'); ?>">
  <form id="TaskModalForm" class="form-horizontal task-form ajax-form" role="form" method="post" action="<?php echo $addTaskAction; ?>">
  	<input type="hidden" name="project_id" value="<?php echo $this->encode($project['id']); ?>"/>
  	<table class="table table-bordered table-condensed table-striped table-hover">
		<thead>
			<tr>
				<th><?php Utils::e('Description'); ?></th>
				<th><?php Utils::e('Deliverable'); ?></th>
				<th><?php Utils::e('Due Date'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="text" class="form-control input-sm" name="task[descriptions][]" maxlength="255" /> 
				</td>
				<td>
					<input type="text" class="form-control input-sm" name="task[deliverables][]" maxlength="255" /> 
				</td>
				<td>
					<input type="text" class="form-control input-sm date-field" name="task[duedates][]" maxlength="10" /> 
				</td>
			</tr>
			<tr>
				<th><?php Utils::e('Responsibles'); ?></th>
				<th><?php Utils::e('Budget'); ?></th>
				<th><?php Utils::e('Files'); ?></th>
			</tr>
			<tr>
				<td>
					<input type="text" class="form-control input-sm staff-search" name="task[responsibles][]" maxlength="255" /> 
					<input type="hidden" class="form-control input-sm charge_ids" name="task[in_charges][]" /> 
				</td>
				<td width="180">
					<input type="number" class="form-control input-sm pull-left task-budget-number-txt" name="task[budgets][]" maxlength="12" /> 
					<select name="task[currency_types][]" class="form-control pull-left input-sm task-budget-combo">
			    		<option value="USD">USD</option>
			    		<option value="TWD">TWD</option>
			    		<option value="RMB">RMB</option>
		    		</select>
				</td>
				<td>
					<input type="file" class="form-control input-sm" name="task[files][]" /> 
				</td>
			</tr>
		</tbody>
	</table>
  </form>
</div>

<form class="hidden" id="CommitForm" method="post" action="<?php echo $commitAction; ?>">
	<input type="hidden" id="project_id" name="project_id" value="<?php echo $recId; ?>" />
	<input type="hidden" id="leader_id" name="leader_id" value="<?php echo $project['leader_id']; ?>" />
	<input type="hidden" id="project_type" name="project_type" value="<?php echo $projectType; ?>" />
</form>