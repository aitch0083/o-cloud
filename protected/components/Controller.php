<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

	const ACCESS_MODE_ALLOW = 'allow';
	const ACCESS_MODE_DENY = 'deny';

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public $today = '';

	public function beforeAction($action) {
   	 	if( parent::beforeAction($action) ) {
	        /* @var $cs CClientScript */
	        $cs = Yii::app()->clientScript;
	        $cs->registerPackage('jquery');
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/bootstrap.min.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/jquery-ui.min.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/jquery.form.min.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/jquery.jqGrid.min.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/jquery.highlight.min.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/jquery.metisMenu.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/i18n/grid.locale-tw.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/summernote.min.js', CClientScript::POS_END);
	        $cs->registerScriptFile( Yii::app()->getBaseUrl() . '/js/generic.behaviors.js', CClientScript::POS_END);
	        $cs->registerCssFile('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css');
	        $cs->registerCssFile(Yii::app()->getBaseUrl() . '/css/south-street/jquery-ui.min.css');
	        $cs->registerCssFile('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');
	        $cs->registerCssFile(Yii::app()->getBaseUrl() . '/css/ui.jqgrid.css');
	        $cs->registerCssFile(Yii::app()->getBaseUrl() . '/css/jquery.metisMenu.css');
	        $cs->registerCssFile(Yii::app()->getBaseUrl() . '/css/summernote.css');
	        $cs->registerCssFile(Yii::app()->getBaseUrl() . '/css/bootstrap.doc.min.css');
	        $cs->registerCssFile(Yii::app()->getBaseUrl() . '/css/generic.styles.css');
	        //controller's action js
	        $moduleId = isset($this->module->id) ? $this->module->id :'';
			$controllerId = isset($this->id) ? $this->id : '';
			$actionId = isset($this->action->id) ? $this->action->id : '';

			if($controllerId !== '' && $actionId !== ''){
				Yii::app()->clientScript->registerScriptFile('/js/'.$moduleId.'/'.$controllerId.'/'.ucwords($controllerId).'Ctrl.js', CClientScript::POS_END);
			}

			$this->today = date('Y-m-d');

	        return true;
    	}
    	return false;
	}

	public function encode($text){
		return Utils::encode($text);
	}

	public function decode($text){
		return Utils::decode($text);
	}

    public function getAccessToken(){
    	$user = Yii::app()->user->getState('user_rec');
    	return $accessToken = $this->encode($user['uName'].'_accesstoken');
    }

    public function restrictActions($actionId, $authCode, $mode=Controller::ACCESS_MODE_ALLOW){
		if(!isset($this->restrictedAreas)){
			return $mode === Controller::ACCESS_MODE_ALLOW ? true : false;
		}else{
			if(isset($this->restrictedAreas[$actionId])){
				return ( intval($authCode) - intval($this->restrictedAreas[$actionId]) >= 0 ) ? true : false;
			}else{
				return $mode === Controller::ACCESS_MODE_ALLOW ? true : false;	
			}
		}
	}
	
}