<?php if(count($categories) > 0): ?>
<select id="ProjectCategory" type="text" class="form-control" name="category_id" >
	<option value="0"><?php Utils::e('Please select...'); ?></option>
	<?php foreach($categories as $idx=>$category): ?>
	<option value="<?php echo $category['id'] ?>"><?php echo $category['title'] ?></option>
	<?php endforeach; ?>
</select>
<?php else: ?>
<div class="alert alert-danger">
	<?php Utils::e('This department doesn\'t accept the new project now! Try another one.'); ?>
	<span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>
</div>	
<?php endif; ?>