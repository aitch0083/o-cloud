<tr id="BizItemRow<?php echo $item['id']; ?>">
	<td><a href="#" class="editable" id="BizItem<?php echo $item['id'] ?>" data-type="text" data-pk="<?php echo $item['id']; ?>" data-url="/ticket/department/editBizItem" data-title="Edit" data-value="<?php echo $item['title']; ?>"><?php echo ($item['title']); ?></a></td>
	<td>
		<button class="btn btn-xs btn-info tipinfos deleteTask<?php echo $item['id']; ?>" cmd="delete_BizItem" cmdVal="<?php echo $item['id']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php Utils::e('Delete'); ?>" type="button"><?php Utils::icon('trash'); ?></button>
	</td>
	<input type="hidden" id="DelBizItemMsg<?php echo $item['id']; ?>" value="<?php Utils::e('Are you sure about deleting [title]?', true, array('title'=>$item['title'])); ?>">
</tr>