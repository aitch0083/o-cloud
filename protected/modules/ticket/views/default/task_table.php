<?php    
$budget = 0;
$daysLeft = 0; 
?>	

<?php if($withHeader): ?>
<table class="table table-bordered table-condensed table-striped table-hover">
	<thead>
		<tr>
			<th><?php Utils::e('Description'); ?></th>
			<th><?php Utils::e('Deliverable'); ?></th>
			<th><?php Utils::e('Due Date'); ?></th>
			<th><?php Utils::e('Responsibles'); ?></th>
			<th><?php Utils::e('Budget'); ?></th>
			<th><?php Utils::e('Files'); ?></th>
			<th width="120"><?php Utils::e('Actions'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php endif; ?>
	
	<?php for($idx = 0 ; $idx < $taskNo ; $idx++): ?>
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
			<td class="text-center">
				<div class="btn-group">
					<?php if($idx >= ($taskNo - 1) ): ?>
					<button class="btn btn-xs btn-info tipinfos newTask<?php echo $idx; ?>" cmd="addTask" cmdVal="" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Plus'); ?>" type="button"><?php Utils::icon('plus'); ?></button>
					<?php endif; ?>
					<button class="btn btn-xs btn-info tipinfos deleteTask<?php echo $idx; ?>" cmd="deleteTask" cmdVal="<?php echo $idx; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Delete'); ?>" type="button"><?php Utils::icon('trash'); ?></button>
				</div>
			</td>
		</tr>
	<?php endfor; ?>

<?php if($withHeader): ?>
		
	</tbody>
</table>
<?php endif; ?>