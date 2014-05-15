<?php 
Yii::import('widgets.AppWidget');

class SystemModalDialog extends AppWidget{

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
		$htmlString = '
		<div id="SystemDialog" class="modal fade bs-modal-sm" tabindex="-1" role="dialog" aria-labelledby="'.Yii::t('yii', 'Message').'" aria-hidden="true">
	      <div class="modal-dialog">
	      	<div id="SystemDialogContent" class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        		<h4 class="modal-title" id="ModalTitle">'.Yii::t('yii', 'Message').'</h4>
      			</div>
      			<div class="modal-body" id="ModalContent">
      			</div>
	      	</div>
	      </div>
   		</div>';
   		echo $htmlString;
	}

}