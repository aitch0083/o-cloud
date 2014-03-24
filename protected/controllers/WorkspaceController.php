<?php

class WorkspaceController extends Controller{

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

	public function actionIndex(){

		$user = Yii::app()->user->getState('user_rec');
		$companyGroup = Yii::app()->user->getState('company_group');

		//fetch menu items via user:
		$menuModel = new MenuItem();
		$topMenuItems = $menuModel->getMenuItems($user['Id'], 1);//type 1
		//$sideMenuItems = $menuModel->getMenuItems($user['Id'], 2, $findKids=true, $companyGroup);//type 2

		$this->render('index', compact(array('topMenuItems', 
											 //'sideMenuItems'
									   )));
	}
}