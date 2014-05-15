<?php 

Yii::import('widgets.AppWidget');

class Paginator extends AppWidget{

	public $htmlOptions = array();

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

	public function render($view=null, $data=null, $return=false){
		
		if($this->config === null){
			throw new Expception('views.widgets.Paginator can\'t handle empty pagination requirement!');
		}

		$page = $this->config['page'];
		$pageNum = $this->config['pageNum'];
        
        $htmlString  = '<ul class="pagination">';
        $htmlString .= '<li '.($page <= 0 ? 'class="disabled"' : '' ).'>';
		$htmlString .=     '<a href="#" cmdVal="'.($page - 1 <= 0 ? 0 : $page - 1 ).'" target="TargetFilterForm">&laquo;</a>';
		$htmlString .= '</li>';
		
		for($i = 0 ; $i < $pageNum ; $i++){
		$htmlString .= '<li '.($i == $page ? 'class="active"' : '' ).'>';
		$htmlString .= '   <a href="#" class="paginationBtn" cmdVal="'.$i.'" target="TargetFilterForm">'.($i+1).'</a>';
		$htmlString .= '</li>';
		}
		
		$htmlString .= '<li '.($page + 1 >= $pageNum ? 'class="disabled"' : '' ).'>';
		$htmlString .= '   <a href="#" cmdVal="'.($page + 1 >= $pageNum ? $pageNum - 1 : $page + 1 ).'" target="TargetFilterForm">&raquo;</a>';
		$htmlString .= '</li>';
        $htmlString .= '</ul>';

        echo $htmlString;
	}
}