<?php 

Yii::import('widgets.AppWidget');

class CalloutBlock extends AppWidget{

	public $htmlOptions = array();
	public $title = '';
	public $content = '';
	public $type = '';

	const INFO = 'info';
	const DANGER = 'danger';
	const WARNING = 'warning';
	const SUCCESS = 'success';

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
		
        $htmlString  = '<div class="bs-callout bs-callout-'.( $this->type !== '' ? $this->type : self::INFO).'">';
        $htmlString .= '  <h4>'.$this->title.'</h4>';
        $htmlString .= '  <p>'.$this->content.'</p>';
        $htmlString .= '</div>';

        echo $htmlString;
	}
}