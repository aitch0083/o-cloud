<?php 

Yii::import('widgets.AppWidget');

class SystemConfirmDialog extends AppWidget{

	public $htmlOptions = array();

	public $items = array();

	public function init(){
		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->id;
		else
			$this->id=$this->htmlOptions['id'];
	}

	public function run(){
		$this->render();
	}

	public function render($view=null, $data=null, $return=false){

		$htmlString = '<div id="ConfirmDialog" title=""><div id="ConfirmDialogContent"></div></div>';
   		echo $htmlString;
	}

}