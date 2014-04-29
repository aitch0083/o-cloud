<?php

class DefaultController extends Controller{

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
		$deleteFormAction = $prefix.'/delete';

		$staff = Yii::app()->user->getState('staff_record');

		$renderPage = ($rendertype === 'usual' && intval($staff['auth_code']) < 256) ? 'index' : 'excel_style';

		$this->render($renderPage, compact('isDone', 'isPublished', 'page', 'pageSize',
										   'startDate', 'endDate',
										   'sortField', 'sortDir', 'statusCode', 'keywords',
										   'records', 'count', 'pageNum', 
										   'columns', 'runningDepartments', 'fromDepartmentId',
										   'editFormAction', 'declineFormAction', 'deleteFormAction',
										   'rendertype'));
	}

	public function actionAdd($startLevel=1, $type='peripheral'){

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
			$leader = $dept->getLeader($deptId);
			$dbAccessResult = null;
			
			$project = new Project();
			$project->attributes = $form;

			//setup default fields
			$now = date('Y-m-d H:i:s');
			$project->from_department_id = $staffRecord['BranchId'];//Creator's Deparment
			$project->leader_id = $leader['Id'];
			$project->created = $now;
			$project->modified = $now;
			$project->dept_path = $path;
			$project->type_id = $form['type_id'];
			$project->estimated_profit = abs(intval($form['estimated_profit']));
			$project->acceptance = htmlspecialchars($form['acceptance']);
			$project->note = htmlspecialchars($form['note']);

			$task = Yii::app()->request->getPost('task');
			$descriptions = $task['descriptions'];
			$deliverables = $task['deliverables'];
			$duedates = $task['duedates'];
			$responsibles = $task['responsibles'];
			$inCharges = $task['in_charges'];
			$budgets = $task['budgets'];
			$currencyTypes = $task['currency_types'];
			$files = isset($_FILES['task']) ? $_FILES['task'] : array();

			//calculate task number
			$project->task_no = count($descriptions);
				
			if($project->save()){
				$project->writeLog($user['Id'], Project::OP_CREATE, $project->primaryKey);

				//create task
				$taskModel = new Task();
				$taskModel->create($project->primaryKey, $user['Id'], $descriptions, $duedates, $responsibles, 
								   $inCharges, $deliverables, $files, $budgets, $currencyTypes);

				$dbAccessResult = array(
					'result' => true,
					'redirect' => '/'.$this->module->id.'/'.$this->id,
					'msg' => Utils::e('Proejct saved. Good luck.', false,
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

		$this->actionGetList($startLevel, 0, false, $type);
	}

	public function actionGetList($startLevel, $parentId=0, $isAjax=true, $type='peripheral'){
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
			$generateTaskTableUrl     = $prefix.'/generateTaskTable';
			$imgUploadUrl             = $prefix.'/uploadImg';
			$staffSearchUrl           = $prefix.'/searchStaff';
			$accessToken = $this->getAccessToken();
			$assignedType = $type;

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
									     'getCategoriesByDeptIdUrl', 'checkDeptOpenUrl', 'projectAddUrl', 'generateTaskTableUrl',
									     'imgUploadUrl', 'staffSearchUrl',
									     'accessToken', 'crumbs', 'projectTypes', 'assignedType'));
		}
	}

	public function actionEdit(){
		$id = $this->decode(Yii::app()->request->getPost('project_id'));

		$project = Project::model()->findByPk($id);
		$contact = $project->contact->staff;
		$auditor = Staff::model()->findByPk($project['leader_id']); 
		$accessToken = $this->getAccessToken();
		$prefix = '/'.$this->module->id.'/'.$this->id;
		$editAction = $prefix.'/updateField';

		//fetch main project types
		$projectModel = new Project();
		$projectTypes = $projectModel->getProjectTypes();

		$projectCategories = $this->actionGetCategories(array($project['department_id']), false);

		$department = new Department();
		$departments = $department->getAll();
		$departments = Utils::buildTree($departments);
		$deptPath = explode(',', $project['dept_path']);
		$prefix 				  = '/'.$this->module->id.'/'.$this->id;
		$searchAction 			  = $prefix.'/'.$this->action->id;
		$addAction 				  = $prefix.'/add';
		$getMenuAction 			  = $prefix.'/getList';
		$addTaskAction            = $prefix.'/addTask';
		$getContactAction  		  = $prefix.'/getContact';
		$checkProjectNameDupUrl   = $prefix.'/searchByKeyword';
		$getCategoriesByDeptIdUrl = $prefix.'/getCategories';
		$checkDeptOpenUrl 		  = $prefix.'/isOpen';
		$projectAddUrl 			  = $prefix.'/add';
		$generateTaskTableUrl     = $prefix.'/generateTaskTable';
		$imgUploadUrl             = $prefix.'/uploadImg';
		$staffSearchUrl           = $prefix.'/searchStaff';
		$editTaskUrl			  = $prefix.'/editTask';
		$updateTaskListUrl        = $prefix.'/updateTaskList';
		$deleteTaskUrl			  = $prefix.'/deleteTask';
		$updateProjectUrl         = $prefix.'/updateFields';
		$commitAction 			  = $prefix.'/commit';
		$accessToken = $this->getAccessToken();

		$taskModel = new Task();
		$tasks = $taskModel->read($id);

		$crumbs = array(
				array( 'link'=>$prefix, 'label'=>Utils::e('Project List', false) ),
				array( 'link'=>$editAction, 'label'=>Utils::e('Edit Project', false) ),
			);

		$this->render('edit', compact('project', 'accessToken', 'editAction', 'addTaskAction', 'crumbs', 
									  'projectTypes', 'projectCategories', 'departments', 
									  'deptPath', 'contact', 'tasks', 'searchAction', 'addAction',
								      'getMenuAction', 'getContactAction', 'checkProjectNameDupUrl',
								      'getCategoriesByDeptIdUrl', 'checkDeptOpenUrl', 'projectAddUrl', 'generateTaskTableUrl',
								      'imgUploadUrl', 'staffSearchUrl', 'editTaskUrl', 'updateTaskListUrl', 'deleteTaskUrl',
								      'updateProjectUrl', 'auditor', 'commitAction',
								      'accessToken'));
	}

	public function actionAddTask(){
		$user = Yii::app()->user->getState('user_rec');

		$id = $this->decode(Yii::app()->request->getPost('project_id'));
		$task = Yii::app()->request->getPost('task');
		$descriptions = $task['descriptions'];
		$deliverables = $task['deliverables'];
		$duedates = $task['duedates'];
		$responsibles = $task['responsibles'];
		$inCharges = $task['in_charges'];
		$budgets = $task['budgets'];
		$currencyTypes = $task['currency_types'];
		$files = isset($_FILES['task']) ? $_FILES['task'] : array();

		//create task
		$taskModel = new Task();
		$rlt = $taskModel->create($id, $user['Id'], $descriptions, $duedates, $responsibles, 
						   $inCharges, $deliverables, $files, $budgets, $currencyTypes);

		if($rlt){	
			//update project task_no
			$project = Project::model()->findByPk($id);
			$project->task_no = $taskModel->getTaskNo($id);
			$project->save();
			echo json_encode(array('rlt'=>true, 'msg'=>Utils::e('Task created.', false)));
		}else{
			echo json_encode(array('rlt'=>false, 'msg'=>Utils::e('Unable to create task!', false)));
		}
	}

	public function actionUpdateTaskList(){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$projectId = $this->decode(Yii::app()->request->getPost('project_id'));

		$tasks = Task::model()->findAll('project_id=:project_id', array(':project_id'=>$projectId));

		$this->renderPartial('task_list', array('tasks'=>$tasks, 'project_id'=>$projectId));
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

	public function actionDelete(){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$id = $this->decode(Yii::app()->request->getPost('project_id'));

		$project = Project::model()->findByPk($id);
		$project->is_canceled = 1;
		$rlt = $project->save();

		$result = array(
			'rlt' => $rlt,
			'msg' => $rlt ? 
					   Utils::e('This proejct is deleted.', false) : 
					   Utils::e('This proejct cannot be deleted.', false)
		);

		echo json_encode($result);
	}

	public function actionGenerateTaskTable($taskNo, $withHeader='true'){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$withHeader = $withHeader === 'true' ? true : false;
		$this->renderPartial('task_table', array('taskNo'=>$taskNo, 'withHeader'=>$withHeader));
	}

	public function actionUploadImg(){

		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$file = $_FILES['file'];

		if(!$file['error']){

			$uploadFolder = Yii::app()->basePath.'/../files/user_uploads/';
			
			$name = md5(rand(100, 200));
	        $ext = explode('.', strtolower($file['name']));
	        $filename = $name . '.' . array_pop($ext);
	        $destination = $uploadFolder . $filename; 
	        $location = $file['tmp_name'];
	        move_uploaded_file($location, $destination);

	        echo '/files/user_uploads/' . $filename;//change this UR
    	}else{
    		echo 'Image Upload Failed! Details:'.print_r($file, true);
    	}
	}

	public function actionSearchStaff($term){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$staff = new Staff();
		$results = $staff->search($term); 

		echo json_encode($results);
	}

	public function actionEditTask($type){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$taskModel = new Task();
		$updateRlt = array(
			'rlt'=>false,
			'msg'=>''
		);
		
		switch($type){
			default:
				$pk = Yii::app()->request->getPost('pk');
				$value = Yii::app()->request->getPost('value');
				$updateRlt['rlt'] = $taskModel->updateField($type, $value, $pk);
				$updateRlt['msg'] = $updateRlt['rlt'] ? Utils::e('Field Updated.', false) : Utils::e('Field Update Failed.' ,false);
				break;
			case 'task_file': 
				$file = $_FILES[0];
				$id = Yii::app()->request->getPost('id');
				$updateRlt['rlt'] = $taskModel->updateFile($file, $id);
				$updateRlt['msg'] = $updateRlt['rlt'] ? Utils::e('File Uploaded.', false) : Utils::e('File Failed.' ,false);
				break;
		}
		
		echo json_encode($updateRlt);
	}

	public function actionDeleteTask(){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$projectId = $this->decode(Yii::app()->request->getPost('project_id'));
		$taskId = $this->decode(Yii::app()->request->getPost('task_id'));

		$rlt = Task::model()->deleteByPk($taskId);
		$taskModel = new Task();

		if($rlt){
			//update project task_no
			$project = Project::model()->findByPk($projectId);
			$project->task_no = $taskModel->getTaskNo($projectId);
			$project->save();
		}

		echo $rlt ? json_encode(array('rlt'=>true, 'msg'=>Utils::e('Task Deleted', false))) : json_encode(array('rlt'=>false, 'msg'=>Utils::e('Unable to delete task.', false)));
	}

	public function actionUpdateField($type){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$projectModel = new Project();
		$updateRlt = array(
			'rlt'=>false,
			'msg'=>''
		);
		
		switch($type){
			default:
				$pk = Yii::app()->request->getPost('pk');
				$value = Yii::app()->request->getPost('value');
				$updateRlt['rlt'] = $projectModel->updateField($type, $value, $pk);
				$updateRlt['msg'] = $updateRlt['rlt'] ? Utils::e('Field Updated.', false) : Utils::e('Field Update Failed.' ,false);
				break;
		}
		
		echo json_encode($updateRlt);
	}

	public function actionUpdateFields(){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$projectModel = new Project();
		$updateRlt = array(
			'rlt'=>false,
			'msg'=>''
		);
		
		$pk = Yii::app()->request->getPost('pk');
		$values = Yii::app()->request->getPost('data');
		$updateRlt['rlt'] = $projectModel->updateFields($values, $pk);
		$updateRlt['msg'] = $updateRlt['rlt'] ? Utils::e('Fields Updated.', false) : Utils::e('Fields Update Failed.' ,false);
		
		echo json_encode($updateRlt);	
	}

	public function actionCommit(){
		if(!Yii::app()->request->isAjaxRequest){
			return;
		}

		$projectModel = new Project();
		$updateRlt = array(
			'rlt'=>false,
			'msg'=>''
		);

		$id = Yii::app()->request->getPost('project_id');		
		$type = Yii::app()->request->getPost('project_type');
		$project = Project::model()->findByPk($id);
		$user = User::model()->findByPk($project['user_id']);
		$staff = $user->staff;

		$userRec = Yii::app()->user->getState('user_rec');

		switch($type){
			case 'project':
				break;
			case 'process':
				break;
			case 'peripheral':
				//notify leader
				$leaderId = Yii::app()->request->getPost('leader_id');
				$leaderStaff = Staff::model()->findByPk($leaderId);
				if($staff){
					$project->is_commited = 1;
					$project->save();
					//Log commite time
					$project->writeLog($userRec['Id'], 'COMMIT', $id);
					//email $staff
					$mail=Yii::app()->Smtpmail;
			        $mail->SetFrom($staff['Mail'], 'From '.$staff['Name']);
			        $mail->Subject = Utils::e('Please verify and audit the project proposed by proposer. Project[project_title]', false, array('proposer'=>$staff['Name'], 'project_title'=>$project['title']));
			        $mail->MsgHTML(Utils::e('Please login into <a href="url">Click Me</a>', false, array('url'=>Yii::app()->createAbsoluteUrl('/ticket/default/audit'))));
			        $mail->AddAddress($leaderStaff['Mail'], 'To '.$leaderStaff['Mail']);
			        $mail->Send();
			        $updateRlt['rlt'] = true;
					$updateRlt['msg'] = Utils::e('Project commited. And auditor had been notified', false, array('auditor'=>$leaderStaff['Name']));
					$updateRlt['mailMsg'] = $mail->ErrorInfo.' TO '.$leaderStaff['Mail'];
				}else{
					$updateRlt['rlt'] = false;
					$updateRlt['msg'] = Utils::e('Unable to commit, because no valid staff record!', false);
				}
				break;
		}

		echo json_encode($updateRlt);	
	}

}
