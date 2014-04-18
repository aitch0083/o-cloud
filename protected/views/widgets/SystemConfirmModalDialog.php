<?php 

Yii::import('widgets.AppWidget');

class SystemConfirmModalDialog extends AppWidget{

	public $htmlOptions = array();

	public $items = array();
	public $config = null;

	public function init(){
		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->id;
		else
			$this->id=$this->htmlOptions['id'];
	}

	public function run(){
		$this->render();
	}


	public function render(){

		if($this->config === null){
			throw new Eception('views.widgets.SystemConfirmModalDialog can\'t handle empty config set!');
		}

		$config = $this->config();

		$htmlString = '
		<div id="SystemDialog" class="modal fade bs-modal-sm" tabindex="-1" role="dialog" aria-labelledby="'.$config['title'].'" aria-hidden="true">
	      <div class="modal-dialog">
	      	<div id="SystemDialogContent" class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="ModalTitle">'.$config['title'].'</h4>
      			</div>
      			<div class="modal-body" id="ModalContent">
      				'.$config['content'].'
      			</div>
      			<div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">'.$config['close_label'].'</button>
			        <button type="button" class="btn btn-primary" cmd="'.$config['save_cmd'].'" cmdVal="'.$config['save_cmd_value'].'">'.$config['save_label'].'</button>
      			</div>
	      	</div>
	      </div>
   		</div>';
   		echo $htmlString;
	}
}