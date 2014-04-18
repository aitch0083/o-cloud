<?php

class DefaultController extends Controller{

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/workspace_item';

	protected $restrictedAreas = array( 
		//only department manager can do the following actions
		'decline'=>4,
		'edit'=>4
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

	public function actionIndex($isDone=0, $isPublished=0, $page=0, $pageSize=30,
								$startDate='', $endDate='', $departmentId='', $fromDepartmentId='',
								$sortField='Project.id', $sortDir='DESC',
								$keywords='', $statusCode='ALL', $operator='and',
								$rendertype='usual'){

		//load records
		$project = new Project();
		$records = $project->getAll($isDone, $isPublished, $page, $pageSize, $startDate, $endDate, $departmentId, $fromDepartmentId, $sortField, $sortDir, $statusCode, $keywords, $operator);
		$count = $project->getAll($isDone, $isPublished, $page, $pageSize, $startDate, $endDate, $departmentId, $fromDepartmentId, $sortField, $sortDir, $statusCode, $keywords, $operator, $counting=true);

		$pageNum = ceil( $count / $pageSize );

		//Columns for render/sort:
		$columns = array(
			'Project.is_done'=>array('label'=>Utils::e('is Done/Undone', false)),
			'Project.publish'=>array('label'=>Utils::e('is Published', false)),
			'Project.title'=>array('label'=>Utils::e('Title', false)),
			'Project.expecting_date'=>array('label'=>Utils::e('Expection Date', false)),
			'Project.category_id'=>array('label'=>Utils::e('Category', false)),
			'Project.department_id'=>array('label'=>Utils::e('Department', false)),
			'Project.id'=>array('label'=>Utils::e('Created Date', false))
		);

		//All the running departments
		$department = new Department();
		$runningDepartments = $department->getOpendList();

		$isDone = intval($isDone);//convert boolean value
		$isPublished = intval($isPublished);//convert boolean value

		$prefix = '/'.$this->module->id.'/'.$this->id;
		$editFormAction = $prefix.'/edit';
		$declineFormAction = $prefix.'/decline';

		$renderPage = $rendertype === 'usual' ? 'index' : 'excel_style';

		$this->render($renderPage, compact('isDone', 'isPublished', 'page', 'pageSize',
										   'startDate', 'endDate',
										   'sortField', 'sortDir', 'statusCode', 'keywords',
										   'records', 'count', 'pageNum', 
										   'columns', 'runningDepartments', 'fromDepartmentId',
										   'editFormAction', 'declineFormAction',
										   'rendertype'));
	}

	public function actionAdd($startLevel=1){

		if(Yii::app()->request->requestType === 'POST'){//for new created record
			
			$postAccessToken = Yii::app()->request->getPost('access_token');
			$accessToken = $this->getAccessToken();
			$user = Yii::app()->user->getState('user_rec');
			$staffRecord = Yii::app()->user->getState('staff_record');
			
			//verify access token
			if($postAccessToken !== $accessToken){//illegal access
				return;
			}
			$form = $_POST;
			$path = implode(',', $form['department']);
			$form['department_id'] = array_pop($form['department']);
			$form['user_id'] = $user['Id'];
			$form['finished_date'] = '0000-00-00';
			if(isset($form['belongs_to_me']) && $form['belongs_to_me'] == '1'){
				$form['contact_id'] = $user['Id'];
			}
			$deptId = $form['department_id'];
			$dept = new Department();
			$contact = $dept->getContact($deptId);
			$dbAccessResult = null;
			
			$project = new Project();
			$project->attributes = $form;

			//setup default fields
			$now = date('Y-m-d H:i:s');
			$project->from_department_id = $staffRecord['BranchId'];//Creator's Deparment
			$project->created = $now;
			$project->modified = $now;
			$project->dept_path = $path;
			$project->note = $form['note'];

			if($project->save()){
				$project->writeLog($user['Id'], Project::OP_CREATE, $project->primaryKey);
				$dbAccessResult = array(
					'result' => true,
					'redirect' => '/'.$this->module->id.'/'.$this->id,
					'msg' => Utils::e('Proejct saved, the contact [{uName}{title}] of the department will get back to you asap.', false,
									array('{uName}'=>$contact['Name'], '{title}'=>$contact['title']))
				);	
			}else{
				$dbAccessResult = array(
					'result'=>true,
					'redirect' => '',
					'msg'=>Utils::e('Unable to save Proejct! Contact Admin: {admin}. Details:{details}', false,
									array('{admin}'=>Yii::app()->params['adminEmail'], '{details}'=>print_r($project->getErrors(), true)))
				);	
			}

			echo json_encode($dbAccessResult);
			
			return;
		}

		$this->actionGetList($startLevel, 0, false);
	}

	public function actionGetList($startLevel, $parentId=0, $isAjax=true){
		$department = new Department();
		$list = $department->getList($startLevel, $parentId);
		$maxLevel = $department->getMaxLevel();

		if($startLevel > $maxLevel){
			return;
		}

		if($isAjax){
			$this->renderPartial('add', compact('startLevel', 'maxLevel', 'list', 'isAjax'));
		}else{
			$user = Yii::app()->user->getState('user_rec');
			$prefix 				  = '/'.$this->module->id.'/'.$this->id;
			$searchAction 			  = $prefix.'/'.$this->action->id;
			$addAction 				  = $prefix.'/add';
			$getMenuAction 			  = $prefix.'/getList';
			$getContactAction  		  = $prefix.'/getContact';
			$checkProjectNameDupUrl   = $prefix.'/searchByKeyword';
			$getCategoriesByDeptIdUrl = $prefix.'/getCategories';
			$checkDeptOpenUrl 		  = $prefix.'/isOpen';
			$projectAddUrl 			  = $prefix.'/add';
			$accessToken = $this->getAccessToken();

			//fetch main project types
			$project = new Project();
			$projectTypes = $project->getProjectTypes();

			$crumbs = array(
				array( 'link'=>$prefix, 'label'=>Utils::e('Project List', false) ),
				array( 'link'=>$addAction, 'label'=>Utils::e('Init Project', false) ),
			);

			$this->render('add', compact('startLevel', 'maxLevel', 'list',
									     'isAjax', 'user', 'searchAction', 'addAction',
									     'getMenuAction', 'getContactAction', 'checkProjectNameDupUrl',
									     'getCategoriesByDeptIdUrl', 'checkDeptOpenUrl', 'projectAddUrl',
									     'accessToken', 'crumbs', 'projectTypes'));
		}
	}

	public function actionEdit(){
		$id = $this->decode(Yii::app()->request->getPost('project_id'));

		$project = Project::model()->findByPk($id);
		$contact = $project->contact->staff;
		$accessToken = $this->getAccessToken();
		$prefix = '/'.$this->module->id.'/'.$this->id;
		$editAction = $prefix.'/'.$this->action->id;

		//fetch main project types
		$projectModel = new Project();
		$projectTypes = $projectModel->getProjectTypes();

		$projectCategories = $this->actionGetCategories(array($project['department_id']), false);

		$department = new Department();
		$departments = $department->getAll();
		$departments = Utils::buildTree($departments);
		$deptPath = explode(',', $project['dept_path']);

		$crumbs = array(
				array( 'link'=>$prefix, 'label'=>Utils::e('Project List', false) ),
				array( 'link'=>$editAction, 'label'=>Utils::e('Edit Project', false) ),
			);

		$this->render('edit', compact('project', 'accessToken', 'editAction', 'crumbs', 
									  'projectTypes', 'projectCategories', 'departments', 
									  'deptPath', 'contact'));
	}

	public function actionDecline(){
		
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$id = $this->decode(Yii::app()->request->getPost('project_id'));

		$project = Project::model()->findByPk($id);
		//$project->findByPk($id);
		$saveRlt = $project->updateByPk($id, array('is_declined'=>1));
		$rlt = array(
			'rlt' => $saveRlt,
			'msg' => $saveRlt ?  
					 Utils::e('Project ['.$project['title'].'] is declined. You can find this item under declined projects.', false): 
					 Utils::e('Unable to decline Proejct! Contact Admin: {admin}. Details:{details}', false,
									array('{admin}'=>Yii::app()->params['adminEmail'], '{details}'=>print_r($project->getErrors(), true)))
		);

		echo json_encode($rlt);
	}

	/**
	 * Get conatct of the specific department. [AJAX-ONLY]
	 * @param int $departmentId department id
	 * @return array contact record
	 */
	public function actionGetContact($departmentId){

		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$department = new Department();
		$contact = $department->getContact($departmentId);

		echo json_encode($contact);
	}

	/**
	 * Get conatct of the specific department. [AJAX-ONLY]
	 * @param string $key keyword for search
	 * @param string $fields field names seperated by comma
	 * @return array search result based on keyword
	 */
	public function actionSearchByKeyword($key, $fields){

		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$project = new Project();
		$dupResult = $project->searchByKeyword($key, $fields);
		$result = null;

		if(!$dupResult){
			$result = array(
				'is_dup'=>false,
				'record'=>$dupResult,
				'msg'=>Utils::e('This proejct name is perfect!', false)
			);
		}else{
			$result = array(
				'is_dup'=>true,
				'record'=>null,
				'msg'=>Utils::e('This proejct duplicates with other one, please choose another.', false)
			);
		}

		echo json_encode($result);
	}

	public function actionIsOpen($departmentId){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$department = new Department();
		$isOpen = $department->isOpen($departmentId);
		$result = array(
			'is_open' => $isOpen == 0 ? false : true,
			'msg' => $isOpen ? '' : Utils::msg(Utils::MSG_DANGER, 'This department doesn\'t accept the new project now! Try another one.', false, 'help-block is_open_msg')
		);

		echo json_encode($result);
	}

	/**
	 * Get conatct of the specific department. [AJAX-ONLY]
	 * @param string $deptId department id
	 * @return array category items for specific department
	 */
	public function actionGetCategories(array $deptIds, $return=false){

		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$project = new Project();
		$deptId = array_pop($deptIds);
		$categories = $project->getProjectCategories($deptId);

		if($return){
			return $categories;
		}

		$this->renderPartial('project_category_list', compact('categories'));
	}

}
