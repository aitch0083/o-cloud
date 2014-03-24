<!DOCTYPE html>
<html lang="zh-tw">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <!-- jQueryUI styles [CUSTOMIZED!!] -->
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/south-street/jquery-ui.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <!-- Generic Styles -->
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/generic.styles.css" rel="stylesheet">

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

  <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.autoheight.js"></script>
  <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/generic.behaviors.js"></script>
  </body>
</html>
