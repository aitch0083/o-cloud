<?php
/* @var $this DefaultController
* All the variables are defined in the DefaultController
*/
$recId = $project['id'];
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
	    <div class="col-sm-10">
	    	<input id="EstimatedProfit" type="number" class="form-control disabled" name="estimated_profit" maxlength="10" minlength="5" placeholder="money here..." value="<?php echo $project['estimated_profit']; ?>" disabled required/>
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
	    			<option <?php echo $project['type_id'] === $type['id'] ? 'selected' : ''; ?> value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
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
	      <input id="ProjectPurpose" type="text" class="form-control" name="purpose" maxlength="100" minlength="5" placeholder="name your purpose" value="<?php echo $project['purpose']; ?>" required/>
	      <span class="help-block"><?php Utils::e('Describe why you need this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-5">
	    <label for="ProjectTargets" class="col-sm-2 control-label"><?php Utils::e('Targets'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectTargets" type="text" class="form-control" name="demands" maxlength="100" minlength="5" placeholder="Who can benefit from this project" value="<?php echo $project['demands']; ?>" required/>
	      <span class="help-block"><?php Utils::e('Who and what will benefit from this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-7">
		<label for="ProjectRange" class="col-sm-2 control-label"><?php Utils::e('Scope'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectRange" type="text" class="form-control" name="apply_range" maxlength="100" minlength="5" placeholder="How many departments will be effected by this project?" value="<?php echo $project['apply_range']; ?>" required/>
	      <span class="help-block"><?php Utils::e('How many departments will be effected by this project?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-8">
		<label for="ProjectVerifiers" class="col-sm-2 control-label"><?php Utils::e('Verifiers'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectVerifiers" type="text" class="form-control" name="verifiers" maxlength="100" minlength="5" placeholder="Who can help you with verifying this project" value="<?php echo $project['verifiers']; ?>" required/>
	      <span class="help-block"><?php Utils::e('Who can help you with verifying this project'); ?></span>
	    </div>
	</div>
	<div class="form-group step-9">
		<label for="ProjectExpectingDate" class="col-sm-2 control-label"><?php Utils::e('Expecting Finish Date'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectExpectingDate" type="text" class="form-control date-field" name="expecting_date" maxlength="10" minlength="10" placeholder="The date you wish it could be finished." value="<?php echo $project['expecting_date']; ?>" required/>
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
		<label for="ProjectNote" class="col-sm-2 control-label"><?php Utils::e('Task Number'); ?>:</label>
	    <div class="col-sm-10">
	      <select id="TaskNo" name="task_no" class="form-control">
	      	 <?php for( $idx=1 ; $idx <= 10 ; $idx++): ?>
	      	 <option <?php echo $project['task_no'] == $idx ? 'selected' : ''; ?> value="<?php echo $idx; ?>"><?php echo Utils::e('{n} tasks.', true, $idx); ?></option>
	      	 <?php endfor; ?>
	      </select>
	      <span class="help-block"><?php Utils::e('How many requied tasks are there to finish this project? At least 1 tasks.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-10">
		<label for="ProjectTask" class="col-sm-2 control-label"><?php Utils::e('Tasks'); ?>:</label>
		<div class="col-sm-10">
			<table class="table table-bordered table-condensed table-striped table-hover">
				<thead>
					<tr>
						<th><?php Utils::e('Description'); ?></th>
						<th><?php Utils::e('Deliverable'); ?></th>
						<th><?php Utils::e('Due Date'); ?></th>
						<th><?php Utils::e('Responsibles'); ?></th>
						<th><?php Utils::e('Budget'); ?></th>
						<th width="120"><?php Utils::e('Actions'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php    $budget = 0;
				         $daysLeft = 0; ?>	
				<?php for($idx = intval($project['task_no']) ; $idx > 0 ; $idx--): ?>
				
					<tr>
						<td>
							<?php switch($idx){
									case 4: echo '蒐集資料';break;
									case 3: echo '寫計劃';break;
									case 2: echo '試行';break;
									case 1: echo '執行公佈';break;
							      } ?>
						</td>
						<td>
							<?php switch($idx){
									case 4: echo '當前包裝作業流程資料';break;
									case 3: echo '流程改善計劃';break;
									case 2: echo '改善流程實際數據';break;
									case 1: echo '實施流程後改善數據證明';break;
							      } ?>
						</td>
						<td><span class="badge badge-danger"> +2days </span></td>
						<td>Aitch</td>
						<td><?php $budget += ($idx*1000); echo 'RMB $',number_format($budget, 2); ?></td>
						<td class="text-center">
							<div class="btn-group">
								<button class="btn btn-xs btn-info tipinfos" cmd="printRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Print'); ?>"><?php Utils::icon('print'); ?></button>
								<button class="btn btn-xs btn-info tipinfos" cmd="viewRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('View'); ?>"><?php Utils::icon('search'); ?></button>
								<button class="btn btn-xs btn-info tipinfos" cmd="editRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Edit'); ?>"><?php Utils::icon('edit'); ?></button>
								<button class="btn btn-xs btn-info tipinfos" cmd="delteRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Cancel'); ?>"><?php Utils::icon('trash'); ?></button>
							</div>
						</td>
					</tr>
				<?php endfor; ?>
					<tr>
						<td colspan="6" class="text-right">Total Budget: NTD $<?php echo number_format($budget, 2); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group step-11">
	    <div class="col-sm-offset-2 col-sm-10">
	      <button type="submit" class="btn btn-default"><?php Utils::e('Submit'); ?></button>
	    </div>
  	</div>
</form>

<!-- Ajax Parameters -->
