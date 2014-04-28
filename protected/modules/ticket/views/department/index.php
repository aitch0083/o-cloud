<?php 
$count = 0;
?>
<table class="table table-responsive table-condensed table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Nickname</th>
			<th>Email</th>
			<th>Branch</th>
			<th>Title</th>
			<th>Lead</th>
			<th>Contact</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($users as $idx=>$user): ?>
		<?php $count++; ?>
		<tr>
			<td><?php echo $user['Id']; ?></td>
			<td><?php echo $user['Name']; ?></td>
			<td><?php echo $user['Nickname']; ?></td>
			<td>
				<a href="#" class="editable" id="UserEmail<?php echo $user['Id'] ?>" data-type="email" data-pk="<?php echo $user['Id']; ?>" data-url="<?php echo '/ticket/department/editUser?type=Mail'; ?>" data-title="Edit"><?php echo $user['Mail']; ?></a>
			</td>
			<td><?php echo $user['Branch']; ?></td>
			<td><?php echo $user['Title']; ?></td>
			<td>
				<?php echo $user['LeadDept'] != '' ? $user['LeadDept'].' &nbsp;<a href="#" class="btn btn-xs btn-danger" cmd="remove_leader" cmdVal="'.$user['Id'].'" target="'.$user['LeadDeptId'].'">'.Utils::icon('remove', true).'</a>' : ''; ?>
			</td>
			<td>
				<?php echo $user['ContactDept'] != '' ? $user['ContactDept'].' &nbsp;<a href="#" class="btn btn-xs btn-danger" cmd="remove_contact" cmdVal="'.$user['Id'].'" target="'.$user['ContactDeptId'].'">'.Utils::icon('remove', true).'</a>' : ''; ?>
			</td>
			<td>
				<div class="btn-group">
					<button class="btn btn-xs btn-info" type="button" cmd="assign_leader" cmdVal="<?php echo $user['Id']; ?>">Audit</button>
					<button class="btn btn-xs btn-info" type="button" cmd="assign_contact" cmdVal="<?php echo $user['Id']; ?>">Contact</button>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="9">
				<p class="text-right">Totoal: <?php echo $count; ?>(people)</p>
			</td>
		</tr>
	</tbody>
</table>

<div id="AssignLeaderDialog">
	<select id="LeaderCombo">
	<?php foreach($departments as $idx=>$department): ?>
		<option value="<?php echo $department['id']; ?>"><?php echo '+',$department['name']; ?></option>
		<?php if(isset($department['children']) && count($department['children']) > 0): ?>
		<?php foreach($department['children'] as $idx=>$department): ?>
			<option value="<?php echo $department['id']; ?>"><?php echo str_repeat('&nbsp;', $department['level']*2), '-', $department['name']; ?></option>
			<?php if(isset($department['children']) && count($department['children']) > 0): ?>
				<?php foreach($department['children'] as $idx=>$department): ?>	
				<option value="<?php echo $department['id']; ?>"><?php echo str_repeat('&nbsp;', $department['level']*3), '-', $department['name']; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	</select>
	<div id="AssignUserRlt"></div>
</div>

<input type="hidden" id="Target" value="" />
<input type="hidden" id="TargetUser" value="" />
<inpur type="hidden" id="TargetOperation" value="" />
<input type="hidden" id="AssignLeaderUrl" value="/ticket/department/assignLeader" />