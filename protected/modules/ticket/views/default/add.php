<?php
/* @var $this DefaultController
* All the variables are defined in the DefaultController
*/
?>

<?php if(!$isAjax): ?>
<form class="form-horizontal project-form ajax-form" role="form" method="post" action="<?php echo $addAction; ?>">
	
	<?php $this->widget('widgets.BreadCrumbs', array('items'=>$crumbs)); ?>
	
	<legend id="ProjectFormLegend">
		<?php Utils::icon('plus'); Utils::e('Initial a Project'); ?>(<?php Utils::e($assignedType); ?>)
	</legend>
	<!-- ACCESS TOKEN -->
	<input type="hidden" id="AccessToken" name="access_token" value="<?php echo $accessToken; ?>" />
	<!-- IS_PUBLISHED -->
	<input type="hidden" id="IsPublished" name="is_published" value="0" />
	<!-- IS_DONE -->
	<input type="hidden" id="IsDone" name="is_done" value="0" />
	<!-- REWARDS -->
	<input type="hidden" id="Rewards" name="rewards" value="0" />
	<!-- CONTACT -->
	<?php if($assignedType !== 'peripheral'): ?>
		<input type="hidden" id="MyProject" name="belongs_to_me" value="0" />
		<input type="hidden" id="ContactId" name="contact_id" value="" />
	<?php else: ?>
		<input type="hidden" id="MyProject" name="belongs_to_me" value="1" />
		<input type="hidden" id="ContactId" name="contact_id" value="<?php echo $user['Id']; ?>" />
	<?php endif; ?>
	<!-- TYPE -->
	<?php foreach($projectTypes as $idx=>$type): ?>
		<?php if($assignedType === strtolower($type['name'])): ?>
		<input type="hidden" id="TypeId" name="type_id" value="<?php echo $type['id']; ?>" />
		<?php endif; ?>
	<?php endforeach; ?>

	<div class="form-group step-1">
	    <label for="DepartmentCombo" class="col-sm-2 control-label"><?php Utils::e('Department'); ?>:</label>
	    <div class="col-sm-10">
	      <select id="DepartmentCombo" name="department[]" class="form-control menu-combo pull-left" startLevel="<?php echo $startLevel; ?>" maxLevel="<?php echo $maxLevel; ?>">
	      	<option value="0"><?php Utils::e('Please Select...'); ?></option>
	      	<?php foreach($list as $idx=>$option): ?>
	      	<option value="<?php echo $option['id']; ?>" is_open="<?php echo $option['is_open']; ?>"><?php Utils::e($option['name']); ?></option>
	      	<?php endforeach; ?>
	      </select>
	    </div>
	</div>
	<div class="form-group step-2 hidden">
	    <label for="ProjectName" class="col-sm-2 control-label"><?php Utils::e('Project Name'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectName" type="text" class="form-control" name="title" maxlength="100" minlength="5" placeholder="name your project..." />
	      <span class="help-block"><?php Utils::e('Project name cannot duplicate with others! Max: 100 chars; Min: 5 chars.'); ?></span>
	    </div>
	    <label for="EstimatedProfit" class="col-sm-2 control-label"><?php Utils::e('Estimated Profit'); ?>:</label>
	    <div class="col-sm-2">
	    	<input id="EstimatedProfit" type="number" class="form-control" name="estimated_profit" maxlength="10" minlength="5" placeholder="money here..." />
	    </div>
	    <div class="col-sm-5">
	    	<select name="currency_type" class="form-control">
	    		<option value="USD">USD</option>
	    		<option value="TWD">TWD</option>
	    		<option value="RMB">RMB</option>
	    	</select>
	    </div>
	    <div class="col-sm-10 col-sm-offset-2">
	    	<span class="help-block"><?php Utils::e('How much profit you think this project can produce?'); ?></span>
	    </div>
	</div>
	
	<?php if($assignedType === 'process'): ?>
	<div class="form-group step-3 hidden">
		<div class="business-items">
		    <label for="ProjectCategory" class="col-sm-2 control-label"><?php Utils::e('Business Items'); ?>:</label>
		    <div class="col-sm-10">
		    	<div class="category-list">
		    		<input type="hidden" name="category_id" value="0" />
		    	</div>
		    	<span class="help-block"><?php Utils::e('Each department provides different categories.'); ?></span>
		    </div>
		</div>
	</div>
	<?php else: ?>
	<input type="hidden" id="CategoryId" name="category_id" value="0" />
	<?php endif; ?>

	<div class="form-group step-4 hidden">
	    <label for="ProjectPurpose" class="col-sm-2 control-label"><?php Utils::e('Purpose'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectPurpose" type="text" class="form-control" name="purpose" maxlength="100" minlength="5" placeholder="name your purpose" />
	      <span class="help-block"><?php Utils::e('Describe why you need this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-5 hidden">
	    <label for="ProjectTargets" class="col-sm-2 control-label"><?php Utils::e('Targets'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectTargets" type="text" class="form-control" name="demands" maxlength="100" minlength="5" placeholder="Who can benefit from this project" />
	      <span class="help-block"><?php Utils::e('Who and what will benefit from this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-7 hidden">
		<label for="ProjectRange" class="col-sm-2 control-label"><?php Utils::e('Scope'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectRange" type="text" class="form-control" name="apply_range" maxlength="100" minlength="5" placeholder="How many departments will be effected by this project?" />
	      <span class="help-block"><?php Utils::e('How many departments will be effected by this project?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-8 hidden">
		<label for="ProjectVerifiers" class="col-sm-2 control-label"><?php Utils::e('Verifiers'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectVerifiers" type="text" class="form-control staff-search" maxlength="100" minlength="5" placeholder="Who can help you with verifying this project"/>
	      <input id="VerifierIds" type="hidden" name="verifiers" value="" />
	      <input id="VerifierNames" type="hidden" name="verifier_names" value="" />  
	      <span class="help-block"><?php Utils::e('Who can help you with verifying this project'); ?></span>
	    </div>
	</div>
	<div class="form-group step-9 hidden">
		<label for="ProjectExpectingDate" class="col-sm-2 control-label"><?php Utils::e('Expecting Finish Date'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectExpectingDate" type="text" class="form-control date-field" name="expecting_date" maxlength="10" minlength="10" placeholder="The date you wish it could be finished." />
	      <span class="help-block"><?php Utils::e('When do you need this project be done?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-6 hidden">
		<label for="ProjectAcceptance" class="col-sm-2 control-label"><?php Utils::e('Acceptance Criteria'); ?>:</label>
	    <div class="col-sm-10">
	      <textarea id="ProjectAcceptance" class="form-control summernote" name="acceptance" required>
	      </textarea>
	      <span class="help-block"><?php Utils::e('Name the criterions for closing this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-10 hidden">
		<label for="ProjectNote" class="col-sm-2 control-label"><?php Utils::e('Note'); ?>:</label>
	    <div class="col-sm-10">
	      <textarea id="ProjectNote" class="form-control summernote" name="note" />
	  	  </textarea>
	  	  <span class="help-block"><?php Utils::e('Anything else you want to say?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-10 hidden">
		<label for="ProjectNote" class="col-sm-2 control-label"><?php Utils::e('Task Number'); ?>:</label>
	    <div class="col-sm-10">
	      <select id="TaskNo" name="task_no" class="form-control">
	      	<option value="0"><?php Utils::e('Select...'); ?></option>
	      	 <?php for( $idx=1 ; $idx <= 10 ; $idx++): ?>
	      	 <option value="<?php echo $idx; ?>"><?php echo Utils::e('{n} tasks.', true, $idx); ?></option>
	      	 <?php endfor; ?>
	      </select>
	      <span class="help-block"><?php Utils::e('How many requied tasks are there to finish this project? At least 4 tasks.'); ?></span>
	    </div>
	</div>
	<div id="TaskTableControl" class="form-group hidden">
		<label for="ProjectTask" class="col-sm-2 control-label"><?php Utils::e('Tasks'); ?>:</label>
		<div id="TaskTableContent" class="col-sm-10"></div>
	</div>
	<div class="form-group step-11 hidden">
	    <div class="col-sm-offset-2 col-sm-10">
	      <button id="SubmitBtn" type="submit" cmd="submit" cmdVal="submit" class="btn btn-default"><?php Utils::e('Submit'); ?></button>
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

<?php else: ?>

	<?php if(count($list) > 0): ?>
	<?php echo Utils::icon('arrow-right pull-left control-label span-'.$startLevel); ?>
	<select name="department[]" class="form-control menu-combo ajax-combo pull-left" startLevel="<?php echo $startLevel; ?>" maxLevel="<?php echo $maxLevel; ?>">
		<option value="0"><?php Utils::e('Please Select...'); ?></option>
		<?php foreach($list as $idx=>$option): ?>
		<option value="<?php echo $option['id']; ?>" is_open="<?php echo $option['is_open']; ?>"><?php Utils::e($option['name']); ?></option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>

<?php endif; ?>
