<table id="BizTable" class="table table-responsive table-condensed table-hover" >
	<thead>
		<tr>
			<th><?php Utils::e('Business Items'); ?></th>
			<th><?php Utils::e('Actions'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($businessItems)): ?>
			<?php foreach($businessItems as $idx=>$item): ?>
			<tr id="BizItemRow<?php echo $item['id']; ?>">
				<td><a href="#" class="editable" id="BizItem<?php echo $item['id'] ?>" data-type="text" data-pk="<?php echo $item['id']; ?>" data-url="<?php echo $editAction; ?>" data-title="Edit" data-value="<?php echo $item['title']; ?>"><?php echo ($item['title']); ?></a></td>
				<td>
					<button class="btn btn-xs btn-info tipinfos deleteTask<?php echo $item['id']; ?>" cmd="delete_BizItem" cmdVal="<?php echo $item['id']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Delete'); ?>" type="button"><?php Utils::icon('trash'); ?></button>
				</td>
				<input type="hidden" id="DelBizItemMsg<?php echo $item['id']; ?>" value="<?php Utils::e('Are you sure about deleting [title]?', true, array('title'=>$item['title'])); ?>">
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<blockquote>
<table class="table table-condensed">
	<thead>
		<tr>
			<th><?php Utils::e('Business Items'); ?></th>
			<th><?php Utils::e('Actions'); ?></th>
		</tr>
	</thead>
	<tbody> 
		<tr>
			<td><input type="text" class="form-control" maxwidth="60" id="BizItemTitle" /></td>
			<td><button type="button" class="btn btn-primary" cmd="add_bi" cmdVal="<?php echo $addAction; ?>"><?php echo Utils::e('Save'); ?></button></td>
		</tr>
	</tbody>
</table>
</blockquote>

<!-- Ajax Parameters -->
<input type="hidden" id="DelBizItemUrl" value="<?php echo $delBizItemUrl; ?>"> 