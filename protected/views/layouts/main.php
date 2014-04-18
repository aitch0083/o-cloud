<!DOCTYPE html>
<html lang="zh-tw">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    
    <div class="wrapper">

      <?php if(!Yii::app()->user->isGuest): //user logined already ?>
      
      <?php endif; ?>

    	<?php echo $content; ?>
	  </div>

    <input type="hidden" id="Controller" value="<?php echo $this->id; ?>" />
    <input type="hidden" id="Action" value="<?php echo 'action',ucwords($this->action->id); ?>" />
    <?php $this->widget('widgets.SystemConfirmDialog'); ?>
    <?php $this->widget('widgets.SystemModalDialog',array()); ?>
  </body>
</html>
