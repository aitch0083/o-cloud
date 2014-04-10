<?php 

/**
 * 
 * Target:
 * 
 * 
*/
Yii::import('widgets.AppWidget');

class SystemTopMenu extends AppWidget{

	public $htmlOptions = array();

	public $brand = '';

	public $items = array();

	public function init(){
		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->id;
		else
			$this->id=$this->htmlOptions['id'];

		if($this->brand === '')
			$this->brand = Yii::app()->name;
	}

	public function run(){
		$this->render();
	}

	public function render(){
		
        if($this->items === null){
			throw new Exception('views.widgets.SystemTopMenu can\'t handle empty record set!' );
		}

		echo '<nav class="navbar navbar-default affix-menu" role="navigation" data-spy="affix" data-offset-top="60" data-offset-bottom="200">';
 		echo ' <div class="container-fluid">';//container for the system menu

 		//<!-- Brand and toggle get grouped for better mobile display -->
 		echo '<div class="navbar-header">
      			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        		<span class="sr-only">Toggle navigation</span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
      			</button>
      			<a class="navbar-brand" href="#">'.$this->brand.'</a>
    		  </div>';

        //<!-- Collect the nav links, forms, and other content for toggling -->
    	echo '<div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1" style="height: 1px;">'; //menu content
        if(count($this->items) > 0){
        	echo CHtml::openTag('ul', array('class'=>'nav navbar-nav')), chr(10);
        	foreach($this->items as $idx => $item){
        		if(isset($item['children']) && count($item['children']) > 0){
        			echo CHtml::openTag('li', array('class'=>'dropdown'));
        			echo CHtml::link($item['label'].'<b class="caret"></b>', '#', array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'));
        			echo CHtml::openTag('ul', array('class'=>'dropdown-menu', 'role'=>'menu'));
        			foreach($item['children'] as $jdx => $subitem){
        				echo CHtml::openTag('li', array('class'=>isset($subitem['active']) ? $subitem['active'] : '' )),chr(10);
        				echo CHtml::link($subitem['label'], $subitem['link']);
        				echo CHtml::closeTag('li');
        			}
        			echo CHtml::closeTag('ul');
        			echo CHtml::cloaseTag('li');
        		}else{
        			echo CHtml::openTag('li', array('class'=>isset($item['active']) ? $item['active'] : '')),chr(10);
        			echo CHtml::link($item['label'], '#');
    				echo CHtml::closeTag('li');
        		}
        	}
        	echo CHtml::closeTag('ul');
        }

    	echo CHtml::openTag('ul', array('class'=>'nav navbar-nav navbar-right'));
    	echo CHtml::openTag('li', array('class'=>'dropdown'));
    	
    	$user = Yii::app()->user->getState('user_rec');
        $staffRec = Yii::app()->user->getState('staff_record');
        $lastLoginTime = Yii::app()->user->getState('last_login_time');
        $prefix = $this->icon('user').' Hi, '.$staffRec['Name'].'@'.$staffRec['Branch'];
    	echo CHtml::link($prefix.'<b class="caret"></b>', '#', array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'));
    	echo CHtml::openTag('ul', array('class'=>'dropdown-menu', 'role'=>'menu'));
        echo CHtml::openTag('li');
        echo CHtml::link($this->icon('flag').Yii::t('yii', 'From:').Yii::app()->request->getUserHostAddress(), '#');
        echo CHtml::closeTag('li');
        echo CHtml::openTag('li', array('class'=>'divider'));
        echo CHtml::closeTag('li');
    	echo CHtml::openTag('li');
    	echo CHtml::link($this->icon('log-out').Yii::t('yii', 'Logout'), '/site/logout');
    	echo CHtml::closeTag('li');
    	echo CHtml::closeTag('ul');

    	echo CHtml::closeTag('li');
    	echo CHtml::closeTag('ul');
    	
    	echo '</div>';//EO://menu content

 		echo ' </div>';//EO: container for the system menu
		echo '</nav>';//EO: nav
	}
}