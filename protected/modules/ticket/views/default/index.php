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
				    <label><b class="glyphicon glyphicon-plane"></b> <?php echo Yii::t('yii', '專案狀態') ?>: </label>
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
				  	<button class="btn btn-xs btn-primary" type="button" cmd="addRecord" cmdVal="<?php echo $addAction; ?>" target="#workspace" ><b class="glyphicon glyphicon-plus"></b> <?php echo Yii::t('yii', '開新專案'); ?></button>
				  	<button class="btn btn-xs btn-info <?php echo $isDone === 0 ? 'active' : '' ?>" type="button" cmd="filterRecord" cmdVal="<?php echo $searchAction,'?isDone=0'; ?>" target="#workspace"><?php Utils::icon('plane');Utils::e('To Others'); ?></button>
				  	<button class="btn btn-xs btn-info <?php echo $isPublished === 0 ? 'active' : '' ?>" type="button" cmd="filterRecord" cmdVal="<?php echo $searchAction,'?isPublished=0'; ?>" target="#workspace"><?php Utils::icon('ok');Utils::e('For My Department'); ?></button>
			  	</div>
			</form>	
		</div>
	</div>

	<?php if(count($records) > 0): ?>
		<table id="GridTable" class="table">
			<thead>
				<tr>
					<th><?php Utils::e('Department'); ?></th>
					<th><?php Utils::e('Type'); ?></th>
					<th><?php Utils::e('Title'); ?></th>
					<th><?php Utils::e('Contact'); ?></th>
					<th><?php Utils::e('Demands'); ?></th>
					<th><?php Utils::e('Status'); ?></th>
					<th><?php Utils::e('Expecting'); ?></th>
					<th><?php Utils::e('Note'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($records as $idx=>$record): ?>
				<tr>
					<td><?php echo $record['department_name']; ?></td>
					<td><?php echo $record['category_name']; ?></td>
					<td><?php echo $record['title']; ?></td>
					<td><?php echo $record['contact_name']; ?></td>
					<td><?php echo $record['demands']; ?></td>
					<td><?php echo $record['is_done'] ? 'Published' : 'NAN'; ?></td>
					<td><?php echo $record['expecting_date']; ?></td>
					<td><?php echo $record['note']; ?></td>
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

<!-- Params -->
<input type="hidden" id="GridTableUrl" value="/ticket/task/read" />
<input type="hidden" id="GridTableRowNum" value="50" />
<input type="hidden" id="GridTableSortname" value="invid" />
<input type="hidden" id="GridTableSortorder" value="desc" />
<input type="hidden" id="GridTableCaption" value="<?php echo Yii::t('yii', 'Current Project'); ?>" />