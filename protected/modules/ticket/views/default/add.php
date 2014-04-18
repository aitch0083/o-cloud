<?php
/* @var $this DefaultController
* All the variables are defined in the DefaultController
*/

?>

<?php if(!$isAjax): ?>
<form class="form-horizontal project-form ajax-form" role="form" method="post" action="<?php echo $addAction; ?>">
	
	<?php $this->widget('widgets.BreadCrumbs', array('items'=>$crumbs)); ?>
	
	<legend id="ProjectFormLegend"><?php Utils::icon('plus'); Utils::e('Initial a Project'); ?></legend>
	<!-- ACCESS TOKEN -->
	<input type="hidden" id="AccessToken" name="access_token" value="<?php echo $accessToken; ?>" />
	<!-- CONTACT -->
	<input type="hidden" id="ContactId" name="contact_id" value="" />
	<!-- IS_PUBLISHED -->
	<input type="hidden" id="IsPublished" name="is_published" value="0" />
	<!-- IS_DONE -->
	<input type="hidden" id="IsDone" name="is_done" value="0" />
	<!-- REWARDS -->
	<input type="hidden" id="Rewards" name="rewards" value="0" />

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
	<div class="form-group step-1">
	    <label for="SelfPromotion" class="col-sm-2 control-label"><?php Utils::e('Assign To Me'); ?>:</label>
	    <div class="col-sm-10">
	        <input id="SelfPromotion" type="checkbox" name="belongs_to_me" value="1" />
	        <span class="help-block"><?php Utils::e('This project belongs to me, it\'s my self-promotion project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-2 hidden">
	    <label for="ProjectName" class="col-sm-2 control-label"><?php Utils::e('Project Name'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectName" type="text" class="form-control" name="title" maxlength="100" minlength="5" placeholder="name your project..." required/>
	      <span class="help-block"><?php Utils::e('Project name cannot duplicate with others! Max: 100 chars; Min: 5 chars.'); ?></span>
	    </div>
	    <label for="EstimatedProfit" class="col-sm-2 control-label"><?php Utils::e('Estimated Profit'); ?>:</label>
	    <div class="col-sm-10">
	    	<input id="EstimatedProfit" type="number" class="form-control" name="estimated_profit" maxlength="10" minlength="5" placeholder="money here..." required/>
	    	<span class="help-block"><?php Utils::e('How much profit you think this project can produce?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-3 hidden">
		<label for="ProjectType" class="col-sm-2 control-label"><?php Utils::e('Project Type'); ?>:</label>
	    <div class="col-sm-10">
	    	<div class="type-list">
	    		<select name="type_id" id="ProjectTypes" class="form-control">
	    			<option value="0"><?php echo Utils::e('All...'); ?></option>
	    			<?php foreach($projectTypes as $idx=>$type): ?>
	    			<option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
	    			<?php endforeach; ?>
	    		</select>
	    	</div>
	    	<span class="help-block"><?php Utils::e('Different types have different audit processes.'); ?></span>
	    </div>
	    <div class="business-items hidden">
		    <label for="ProjectCategory" class="col-sm-2 control-label"><?php Utils::e('Business Items'); ?>:</label>
		    <div class="col-sm-10">
		    	<div class="category-list">
		    		<input type="hidden" name="category_id" value="0" />
		    	</div>
		    	<span class="help-block"><?php Utils::e('Each department provides different categories.'); ?></span>
		    </div>
		</div>
	</div>
	<div class="form-group step-4 hidden">
	    <label for="ProjectPurpose" class="col-sm-2 control-label"><?php Utils::e('Purpose'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectPurpose" type="text" class="form-control" name="purpose" maxlength="100" minlength="5" placeholder="name your purpose" required/>
	      <span class="help-block"><?php Utils::e('Describe why you need this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-5 hidden">
	    <label for="ProjectTargets" class="col-sm-2 control-label"><?php Utils::e('Targets'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectTargets" type="text" class="form-control" name="demands" maxlength="100" minlength="5" placeholder="Who can benefit from this project" required/>
	      <span class="help-block"><?php Utils::e('Who and what will benefit from this project.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-7 hidden">
		<label for="ProjectRange" class="col-sm-2 control-label"><?php Utils::e('Scope'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectRange" type="text" class="form-control" name="apply_range" maxlength="100" minlength="5" placeholder="How many departments will be effected by this project?" required/>
	      <span class="help-block"><?php Utils::e('How many departments will be effected by this project?'); ?></span>
	    </div>
	</div>
	<div class="form-group step-8 hidden">
		<label for="ProjectVerifiers" class="col-sm-2 control-label"><?php Utils::e('Verifiers'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectVerifiers" type="text" class="form-control" name="verifiers" maxlength="100" minlength="5" placeholder="Who can help you with verifying this project" required/>
	      <span class="help-block"><?php Utils::e('Who can help you with verifying this project'); ?></span>
	    </div>
	</div>
	<div class="form-group step-9 hidden">
		<label for="ProjectExpectingDate" class="col-sm-2 control-label"><?php Utils::e('Expecting Finish Date'); ?>:</label>
	    <div class="col-sm-10">
	      <input id="ProjectExpectingDate" type="text" class="form-control date-field" name="expecting_date" maxlength="10" minlength="10" placeholder="The date you wish it could be finished." required/>
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
	      	 <?php for( $idx=1 ; $idx <= 10 ; $idx++): ?>
	      	 <option value="<?php echo $idx; ?>"><?php echo Utils::e('{n} tasks.', true, $idx); ?></option>
	      	 <?php endfor; ?>
	      </select>
	      <span class="help-block"><?php Utils::e('How many requied tasks are there to finish this project? At least 4 tasks.'); ?></span>
	    </div>
	</div>
	<div class="form-group step-11 hidden">
	    <div class="col-sm-offset-2 col-sm-10">
	      <button type="submit" class="btn btn-default"><?php Utils::e('Submit'); ?></button>
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
