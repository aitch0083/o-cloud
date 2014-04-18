<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;

?>

<!-- Workspace Styles -->
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/workspace.styles.css" rel="stylesheet">

<?php $this->widget('widgets.SystemTopMenu', 
					array(
						'items'=>$topMenuItems
					)); 
	  $this->widget('widgets.SystemLeftMenu',
	  				array(
	  					'staffRecord'=>$staffRecord
	  					//'items'=>$sideMenuItems
	  				));
?>

<div id="page-wrapper">
	<iframe id="workspace" class="autoHeight" src="/ticket/default/index?fromDepartmentId=<?php echo $staffRecord['BranchId']; ?>&rendertype=list" frameborder="0" style="overflow:scroll;"></iframe>
</div>


