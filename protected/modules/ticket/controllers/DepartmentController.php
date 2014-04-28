<?php

class DepartmentController extends Controller{

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/workspace_item';

	protected $restrictedAreas = array( 
		//only department manager can do the following actions
		'decline'=>1,
		'edit'=>1
	);

	/**
	 * @return array action filters
	 */
	public function filters(){
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules(){
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*')
			)
		);
	}

	public function beforeAction($action){
		if(parent::beforeAction($action)){
			$staffRec = Yii::app()->user->getState('staff_record');
			return $this->restrictActions($action->id, $staffRec['auth_code']);
		}
		return false;
	}

	public function actionIndex(){
		$userModel = new User();
		$users = $userModel->getAllUsers();

		$departmentModel = new Department();
		$departments = $departmentModel->getAll();
		$departments = Utils::buildTree($departments);

		$this->render('index', compact('users', 'departments'));
 	}

 	public function actionEditUser($type){
 		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$userModel = new User();
		$updateRlt = array(
			'rlt'=>false,
			'msg'=>''
		);
		
		switch($type){
			default:
				$pk = Yii::app()->request->getPost('pk');
				$value = Yii::app()->request->getPost('value');
				$updateRlt['rlt'] = $userModel->updateField($type, $value, $pk);
				$updateRlt['msg'] = $updateRlt['rlt'] ? Utils::e('Field Updated.', false) : Utils::e('Field Update Failed.' ,false);
				break;
		}
		
		echo json_encode($updateRlt);
 	}

 	public function actionAssignLeader($type='assign'){
 		if(!Yii::app()->request->isAjaxRequest){
			return;
		}
		
		$departmentId = Yii::app()->request->getPost('department_id');	
		$userId = Yii::app()->request->getPost('user_id');	
		$operation = Yii::app()->request->getPost('operation');

		if($operation === 'remove_leader' || $operation === 'remove_contact'){
			$departmentId = Yii::app()->request->getPost('target');
		}	

		$departmentModel = new Department();
		if($operation === 'assign_leader' || $operation === 'assign_contact'){
			if($departmentModel->assignLeader($departmentId, $userId, $operation)){
				echo json_encode(array(
						'rlt'=>true,
						'msg'=>Utils::e('Leader assigned.', false)
					 ));
			}else{
				echo json_encode(array(
						'rlt'=>true,
						'msg'=>Utils::e('Uanble to assign.', false)
					 ));
			}
		}else if($operation === 'remove_leader' || $operation === 'remove_contact'){
			if($departmentModel->removeLeader($departmentId, $userId, $operation)){
				echo json_encode(array(
						'rlt'=>true,
						'msg'=>Utils::e('Leader removed.', false)
					 ));
			}else{
				echo json_encode(array(
						'rlt'=>true,
						'msg'=>Utils::e('Uanble to remove.', false),
						'department'=>$departmentId,
						'userId'=>$userId
					 ));
			}
		}else{
			return false;
		}
 	}

}