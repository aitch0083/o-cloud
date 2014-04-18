
<!DOCTYPE html>
<html lang="zh-tw">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     
    <!-- Bootstrap -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body style="padding:2px;">
    
    <?php echo $content; ?>

    <?php $this->widget('widgets.SystemConfirmDialog'); ?>
    <?php $this->widget('widgets.SystemModalDialog',array()); ?>

    <input type="hidden" id="Controller" value="<?php echo $this->id; ?>" />
    <input type="hidden" id="Action" value="<?php echo 'action',ucwords($this->action->id); ?>" />
  
  </body>
</html>
