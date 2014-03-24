<?php 

Yii::import('widgets.AppWidget');

class SystemLeftMenu extends AppWidget{

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
			throw new Exception('views.widgets.SystemTopMenu can\'t handle empty record set!' );
		}

		$optHtml  = '<nav class="navbar-default navbar-static-side" role="navigation">';
		$optHtml .= '<div class="sidebar-collapse">';

		$optHtml .= '<ul class="nav" id="side-menu">';

		//Search bar
		$optHtml .= '<li class="sidebar-search">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">'.$this->icon('search').'</button>
                            </span>
                        </div>
                        <!-- /input-group -->
                     </li>';
        //Dashboard
        $optHtml .= '<li>
                        <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/workspace/">'.$this->icon('dashboard').' Dashboard</a>
                    </li>';
	    $optHtml .= '<li>
                        <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/workspace/">'.$this->icon('stats').' Sales Records</a>
                    </li>';                    
        //Iterate the items

        foreach($this->items as $idx=>$item){
        	if(!isset($item['children'])){
        		//Goto target menu url
        		$optHtml .= '<li> <a href="#" class="menuBtn" cmd="gotoTarget" cmdID="'.$item['ModuleId'].'">'.$this->icon('hand-right').' '.$item['label'].'</a> </li>';
        	}else{
        		//Open menu
                $optHtml .= '<li><a href="#" class="menuBtn" cmd="openMenu" cmdID="'.$item['ModuleId'].'">'.$this->icon('hand-right').' '.$item['label'].' '.$this->icon('chevron-right').'</a></li>';
        	}
        }

		$optHtml .= '</ul>';//EO: ul.nav

		$optHtml .= '</div>';//EO: div.sidebar-collapse
		$optHtml .= '</nav>';
		echo $optHtml;
	}
}