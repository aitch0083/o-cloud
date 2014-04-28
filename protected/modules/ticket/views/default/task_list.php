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
				<a class="function-control" href="<?php echo $task['file'];  ?>"><?php Utils::icon('download'); ?></a>
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
						<input type="hidden" name="project_id" value="<?php echo $this->encode($project_id); ?>"/>
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