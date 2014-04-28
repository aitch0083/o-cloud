<?php
/* @var $this DefaultController */
$user = Yii::app()->user->getState('user_rec');
$searchAction = '/'.$this->module->id.'/'.$this->id.'/'.$this->action->id;
$addAction = '/'.$this->module->id.'/'.$this->id.'/add';
?>

<div>
	<div class="navbar navbar-default">
		<div class="container-fluid">
			<form id="TargetFilterForm" class="navbar-form navbar-left" role="search" action="<?php echo $searchAction; ?>" method="get">
				<input type="hidden" name="page" value="<?php echo $page; ?>"/>
				<input type="hidden" name="pageSize" value="<?php echo $pageSize; ?>"/>
				<div class="form-group">
				    <label><b class="glyphicon glyphicon-info-sign"></b> <?php echo Yii::t('yii', '狀態') ?>: </label>
					<select name="statusCode" id="FilterCustomer">
						<option value="ALL" <?php echo $statusCode === 'ALL' ? 'selected' : ''; ?>>All...</option>
						<option value="TODO" <?php echo $statusCode === 'TODO' ? 'selected' : ''; ?>>To Do</option>
						<option value="RUNNING" <?php echo $statusCode === 'RUNNING' ? 'selected' : ''; ?>>Running</option>
						<option value="REVIEWING" <?php echo $statusCode === 'REVIEWING' ? 'selected' : ''; ?>>Reviewing</option>
						<option value="DONE" <?php echo $statusCode === 'DONE' ? 'selected' : ''; ?>>Done</option>
					</select>
			  	</div>
				<div class="form-group">
				    <label><b class="glyphicon glyphicon-plane"></b> <?php echo Yii::t('yii', '完成？') ?>: </label>
					<select name="isDone" id="FilterCustomer">
						<option value="0" <?php echo $isDone ? '' : 'selected'; ?>>未完成</option>
						<option value="1" <?php echo $isDone ? 'selected' : ''; ?>>已完成</option>
					</select>
			  	</div>
			  	<div class="form-group">
				    <label><b class="glyphicon glyphicon-ok"></b> <?php echo Yii::t('yii', '成案？') ?>: </label>
					<select name="isPublished" id="FilterCustomer">
						<option value="0" <?php echo $isPublished ? '' : 'selected'; ?>>未成案</option>
						<option value="1" <?php echo $isPublished ? 'selected' : ''; ?>>已成案</option>
					</select>
			  	</div>
				<div class="form-group">
				    <label><b class="glyphicon glyphicon-heart"></b> <?php echo Yii::t('yii', '接案單位') ?>: </label>
					<select name="customerId" id="FilterCustomer">
						<option value="0">全部...</option>
						<?php foreach($runningDepartments as $idx=>$dept): ?>
						<option value="<?php $dept['id']; ?>"><?php echo $dept['name']; ?></option>
						<?php endforeach; ?>
					</select>
			  	</div>
				<div class="form-group">
				    <label><b class="glyphicon glyphicon-list"></b> <?php echo Yii::t('yii', '顯示筆數') ?>:</label>
					<select name="pageSize" id="FilterCustomer">
						<option value="30" <?php echo $pageSize == 30 ? 'selected' : '' ?> >30</option>
						<option value="50" <?php echo $pageSize == 50 ? 'selected' : '' ?> >50</option>
						<option value="100" <?php echo $pageSize == 100 ? 'selected' : '' ?> >100</option>
						<option value="150" <?php echo $pageSize == 150 ? 'selected' : '' ?> >150</option>
					</select>
			  	</div>
			  	<div class="form-group">
				    <label><b class="glyphicon glyphicon-sort"></b> <?php echo Yii::t('yii', '排序') ?>:</label>
					<select name="sortField" id="FilterCustomer">
						<?php foreach($columns as $key=>$column): ?>
						<option value="<?php echo $key ?>" <?php echo  $sortField === $key ? 'selected' : '' ?> ><?php echo $column['label']; ?></option>
						<?php endforeach; ?>
					</select>
					<select name="sortDir" id="FilterCustomer">
						<option value="ASC" <?php echo $sortDir === 'ASC' ? 'selected' : ''; ?> >昇冪</option>
						<option value="DESC" <?php echo $sortDir === 'DESC' ? 'selected' : ''; ?> >降冪</option>
					</select>
			  	</div>
			  	<div class="form-group">
				    <label><b class="glyphicon glyphicon-calendar"></b> <?php echo Yii::t('yii', '搜尋區間') ?>:</label>
				    <input type="text" name="startDate" class="form-control input-sm date-field" placeholder="開始時間" value="<?php echo $startDate; ?>" maxlength="10"> ~ 
				    <input type="text" name="endDate" class="form-control input-sm date-field" placeholder="結束時間" value="<?php echo $endDate; ?>" maxlength="10">
			  	</div>
			  	<div class="form-group">
			  		<label><b class="glyphicon glyphicon-search"></b> <?php echo Yii::t('yii', '搜尋') ?>:</label>
			        <input type="text" name="keywords" class="form-control input-sm" placeholder="Keywords..." value="<?php echo $keywords; ?>" maxlength="20">
			  	</div>
			  	<div class="btn-group">
				  	<button class="btn btn-xs btn-default" type="submit"><span class="glyphicon glyphicon-play"></span></button>
				  	<button class="btn btn-xs btn-info <?php echo $isDone === 0 ? '' : 'active' ?>" type="button" cmd="filterRecord" cmdVal="<?php echo $searchAction,'?isDone=0'; ?>" target="#workspace"><?php Utils::icon('plane');Utils::e('Undone'); ?></button>
				  	<button class="btn btn-xs btn-info <?php echo $isPublished === 0 ? '' : 'active' ?>" type="button" cmd="filterRecord" cmdVal="<?php echo $searchAction,'?isPublished=0'; ?>" target="#workspace"><?php Utils::icon('ok');Utils::e('UnPublished'); ?></button>
			  	</div>
			</form>	
		</div>
	</div>

	<h4 class="text-info">
		<?php 
		    $listTitle = $fromDepartmentId !== '' ? Utils::e('On Going Projects') : Utils::e('My Team\'s Projects');
		    echo $listTitle;
		?>
		<div class="btn-group">
		    <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
		      <?php echo Yii::t('yii', '開新專案'); ?>
		      <span class="caret"></span>
		    </button>
		    <ul class="dropdown-menu">
		      <li><a class="btn btn-xs" href="#" cmd="addRecord" cmdVal="<?php echo $addAction,'?startLevel=1&type=project'; ?>" target="#workspace"><?php Utils::e('Project'); ?></a></li>
		      <li><a class="btn btn-xs" href="#" cmd="addRecord" cmdVal="<?php echo $addAction,'?startLevel=1&type=process'; ?>" target="#workspace"><?php Utils::e('Process'); ?></a></li>
		      <li><a class="btn btn-xs" href="#" cmd="addRecord" cmdVal="<?php echo $addAction,'?startLevel=1&type=peripheral'; ?>" target="#workspace"><?php Utils::e('Peripheral'); ?></a></li>
		    </ul>
		</div>
	</h4>

	<?php if(count($records) > 0): ?>
		<table id="GridTable" class="table table-responsive table-condensed table-hover">
			<thead>
				<tr>
					<th width="300"><?php Utils::e('Title'); ?></th>
					<th><?php Utils::e('Dept'); ?></th>
					<th><?php Utils::e('Type'); ?></th>
					<th><?php Utils::e('Contact'); ?></th>
					<th><?php Utils::e('Status'); ?></th>
					<th><?php Utils::e('Expecting'); ?></th>
					<th><?php Utils::e('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($records as $idx=>$record): ?>
				<?php    $recId = $record['id']; ?>
				<tr class="success target_rec<?php echo $recId; ?>">
					<td><?php echo $record['title']; ?></td>
					<td><?php echo $record['department_name']; ?></td>
					<td><?php echo $record['category_name']; ?></td>
					<td><?php echo $record['contact_name']; ?></td>
					<td>
						<?php echo Utils::eBadge($record['status']); ?>
					</td>
					<td>
						<?php 
							  $dateDiff = date_diff(date_create($this->today), date_create($record['expecting_date']));  
							  echo $record['expecting_date'];
							  $dayDiff = intval($dateDiff->format('%a'));		
							  if($dayDiff > 0){
						  	  	echo Utils::eBadge($dayDiff.' days', true, 'badge-info');
						  	  }else{
						  	  	echo Utils::eBadge('DUED!!', true, 'badge-danger');
						  	  }
						?>
					</td>
					<td>
						<div class="btn-group">
							<button class="btn btn-xs btn-info tipinfos" cmd="viewRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('View'); ?>"><?php Utils::icon('search'); ?></button>
							
							<?php if($record['user_id'] === $user['Id']): ?>
							<button class="btn btn-xs btn-info tipinfos" cmd="editRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Edit'); ?>"><?php Utils::icon('edit'); ?></button>
							<button class="btn btn-xs btn-info tipinfos" cmd="deleteRecord" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Delete'); ?>"><?php Utils::icon('trash'); ?></button>
							<?php endif; ?>

							<?php if(!$record['is_published'] && !$record['is_declined'] && $staff['auth_code'] >= 256): ?>
							<!-- For Freeman only -->
							<!-- Initial Project will mail to the superviors to the very top. -->
							<button class="btn btn-xs btn-default tipinfos" cmd="accept_prj" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Accept'); ?>"><?php Utils::icon('ok'); ?></button>
							<button class="btn btn-xs btn-danger tipinfos" cmd="decline_prj" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Suspend'); ?>"><?php Utils::icon('remove'); ?></button>
							<button class="btn btn-xs btn-warning tipinfos" cmd="review_prj" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Review'); ?>"><?php Utils::icon('edit'); ?></button>
							<?php endif; ?>

							<?php if($record['is_published'] && !$record['is_done'] && !$record['is_suspend']): ?>
							<button class="btn btn-xs btn-warning tipinfos" cmd="suspend_prj" cmdVal="<?php echo $recId; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Suspend'); ?>"><?php Utils::icon('minus'); ?></button>
							<?php endif; ?>

							<!-- Action Parameters -->
							<input type="hidden" id="AcceptProjectMsg<?php echo $recId; ?>" value="<?php Utils::e('Are you sure about accepting this project? '.chr(10).'Project:['.$record['title'].'] '.chr(10).' Once accepted, it cannot be reversed!'); ?>" />
							<input type="hidden" id="DeclineProjectMsg<?php echo $recId; ?>" value="<?php Utils::e('Are you sure about declining this project? '.chr(10).'Project:['.$record['title'].'] '.chr(10).' Once declined, it cannot be reversed!'); ?>" />
							<input type="hidden" id="DeleteProjectMsg<?php echo $recId; ?>" value="<?php Utils::e('Are you sure about delete this project? '.chr(10).'Project:['.$record['title'].'] '.chr(10).' Once deleted, it cannot be reversed!'); ?>" />
							<!-- Action Forms -->
							<form class="hidden" id="EditProjectActionForm<?php echo $recId; ?>" method="post" action="<?php echo $editFormAction; ?>">
								<input type="hidden" name="project_id" value="<?php echo Utils::encode($recId); ?>" />
							</form>
							<form class="hidden" id="DeclineProjectActionForm<?php echo $recId; ?>" method="post" action="<?php echo $declineFormAction; ?>">
								<input type="hidden" name="project_id" value="<?php echo Utils::encode($recId); ?>" />
							</form>
							<form class="hidden" id="DeleteProjectActionForm<?php echo $recId; ?>" method="post" action="<?php echo $deleteFormAction; ?>">
								<input type="hidden" name="project_id" value="<?php echo Utils::encode($recId); ?>" />
							</form>
						</div>
					</td>
				</tr>
				<tr class="target_rec<?php echo $recId; ?>">
					<td colspan="9">
						<dl class="record-dl">
							<dt><?php Utils::e('Is Done?'); ?></dt>
							<dd><?php echo Utils::eLabel($record['is_done'] ? 'Done' : 'Undone', $record['is_done']); ?></dd>
						</dl>
						<dl class="record-dl">
							<dt><?php Utils::e('Is Published?'); ?></dt>
							<dd><?php echo Utils::eLabel($record['is_published'] ? 'Signed' : 'Not Signed', false); ?></dd>
						</dl>
						<dl class="record-dl">
							<dt><?php Utils::e('Targets'); ?></dt>
							<dd><?php echo $record['demands']; ?></dd>
						</dl>
						<dl class="record-dl">
							<dt><?php Utils::e('Scope'); ?></dt>
							<dd><?php echo $record['apply_range']; ?></dd>
						</dl>
						<dl class="record-dl">
							<dt><?php Utils::e('Task'); ?></dt>
							<dd class="text-center"><?php echo $record['task_no']; ?></dd>
						</dl>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table> 
	<?php else: ?>
		<?php $this->widget('widgets.CalloutBlock', array( 'title'=>Utils::e('No Matching Records', false), 'content'=>Utils::e('The current filters cannot fetch mathing records.', false) )); ?>
	<?php endif; ?>
    <div id="GridTablePager" class="pagination">
    	<?php $this->widget('widgets.Paginator', array( 'config'=>array('page'=>$page, 'pageNum'=>$pageNum) )); ?>
    </div> 

</div>

<div id="ModalConfirm"></div>

<!-- Params -->
<input type="hidden" id="GridTableUrl" value="/ticket/task/read" />
<input type="hidden" id="GridTableRowNum" value="50" />
<input type="hidden" id="GridTableSortname" value="invid" />
<input type="hidden" id="GridTableSortorder" value="desc" />
<input type="hidden" id="GridTableCaption" value="<?php echo Yii::t('yii', 'Current Project'); ?>" />
