<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div class="col-md-5 col-md-offset-4">
	<div class="panel panel-danger top-buffer">
		<div class="panel-heading"><h2 class="panel-title"><?php echo Yii::app()->name, '::Login'; ?></h2></div>

		<div class="panel-body">

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'login-form',
			'enableClientValidation'=>true,
			'focus'=>array($model, 'username'),
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
			'htmlOptions'=>array(
				'class'=>'form-horizontal'
			)
		)); ?>

			<?php echo $form->errorSummary($model, Yii::t('yii', 'Please fix following problems:'), null, array('class'=>'alert alert-danger')); ?>

			<p class="note text-info">Fields with <span class="required text-danger">*</span> are required.</p>

			<div class="form-group">
				<?php echo $form->labelEx($model,'username', array('class'=>'col-sm-4 control-label')); ?>
				<div class="col-sm-8">
					<?php echo $form->textField($model,'username', array('class'=>'form-control focus')); ?>
					<?php echo $form->error($model,'username', array('class'=>'text-danger')); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'password', array('class'=>'col-sm-4 control-label')); ?>
				<div class="col-sm-8">
					<?php echo $form->passwordField($model,'password', array('class'=>'form-control')); ?>
					<?php echo $form->error($model,'password', array('class'=>'text-danger')); ?>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-9">
				<?php echo CHtml::submitButton(Yii::t('yii', 'Login'), array('class'=>'btn btn-primary')); ?>
				</div>
			</div>

		<?php $this->endWidget(); ?>

		</div>

		<div class="panel-footer">
			<h3><?php echo CHtml::image('/images/icon.png', '', array('width'=>50, 'height'=>50)); ?>大阪京實業股份有限公司</h3>
			<p><small>TW:新北市三重區重新路五段609巷10號8樓</small> <small class="pull-right visible-lg">P:<tel>886-2-22782200</tel></small></p>
			<p><small>CN:深圳市寶安區西鄉街黃田村西部開發區</small> <small class="pull-right visible-lg">P:<tel>86-755-27512824</tel></small></p>
		</div>

	</div>
</div><!-- form -->
