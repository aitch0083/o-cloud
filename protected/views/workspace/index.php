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
	<iframe id="workspace" class="autoHeight" src="/order/sales" frameborder="0" scrolling="no"></iframe>
</div>


