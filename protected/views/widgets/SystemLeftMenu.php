<?php 

Yii::import('widgets.AppWidget');

class SystemLeftMenu extends AppWidget{

	public $htmlOptions = array();

	public $items = array();
    public $staffRecord = array();

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

		if($this->items === null){
			throw new Exception('views.widgets.SystemTopMenu can\'t handle empty record set!' );
		}

		$optHtml  = '<nav class="navbar-default navbar-static-side" role="navigation">';
		//$optHtml .= '<div class="sidebar-collapse">';

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
        /*$optHtml .= '<li>
                        <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="">'.$this->icon('dashboard').' Dashboard</a>
                    </li>';
	    $optHtml .= '<li>
                        <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/order/sales">'.$this->icon('stats').' Sales Records</a>
                    </li>'; */

        $optHtml .= '<li class="active">
                        <a href="#">'.$this->icon('list-alt').' '.Utils::e('Projects', false).' <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level" style="height: auto;">
                            <li>
                                <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/ticket/default/index?fromDepartmentId='.$this->staffRecord['BranchId'].'">'.$this->icon('chevron-right').Utils::e('To Others', false).'</a>
                            </li>
                            <li>
                                <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/ticket/default/index?departmentId='.$this->staffRecord['BranchId'].'">'.$this->icon('chevron-right').Utils::e('For My Team', false).'</a>
                            </li>
                            <li>
                                <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/ticket/default/index?fromDepartmentId='.$this->staffRecord['BranchId'].'&rendertype=list">'.$this->icon('chevron-right').Utils::e('Detail List', false).'</a>
                            </li>
                        </ul>
                    </li>';                    

        $user = Yii::app()->user->getState('staff_record');
        if($user['auth_code'] > 1){
            $optHtml .= '<li class="active">
                            <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/ticket/default/index?isCommit=1&isPublished=0">'.$this->icon('list-alt').' '.Utils::e('Audition', false).'</a>
                            <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/ticket/default/index?isCommit=1&isPublished=1">'.$this->icon('list-alt').' '.Utils::e('Auditted', false).'</a>
                            <a href="#" class="menuBtn" cmd="changeWorkspace" cmdVal="/ticket/department/itemList">'.$this->icon('list-alt').' '.Utils::e('Business Items', false).'</a>
                         </li>';                    
        }

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

		//$optHtml .= '</div>';//EO: div.sidebar-collapse
		$optHtml .= '</nav>';
		echo $optHtml;
	}
}