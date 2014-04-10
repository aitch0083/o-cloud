<?php 

Yii::import('widgets.AppWidget');

class BreadCrumbs extends AppWidget{

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

	public function render(){
		
        if($this->items === null){
			throw new Exception('views.widgets.BreadCrumb can\'t handle empty record set!' );
		}

        $itemNum = count($this->items);
		$htmlString  = '<ul class="breadcrumb">';
        foreach($this->items as $idx=>$item){
            $htmlString .= '<li '.($idx === $itemNum - 1 ? 'class="active"' : '').'>'.($idx === $itemNum - 1 ? $item['label'] : '<a href="'.$item['link'].'" >'.$item['label'].'</a>').'</li>';
        }
        $htmlString .= '</ul>';

        echo $htmlString;
	}
}