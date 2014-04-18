<?php

class Project extends CActiveRecord{

	public static $db = null;
	private static $targetDB = 'ocdb';

	const OP_CREATE = 'CREATE';
	const OP_ACCEPT = 'ACCEPT';
	const OP_DECLINE = 'DECLINE';
	const OP_SUSPEND = 'OP_SUSPEND';
	const OP_CANCEL = 'OP_CANCEL';
	const OP_REVIEW = 'OP_REVIEW';

	/**
	 * @return resource target database conection
	*/
	protected static function getDbInConnection(){
		if(self::$db !== null){
			return self::$db;
		}else{
			self::$db = Yii::app()->ocdb;
			if(self::$db instanceof CDbConnection){
				self::$db->setActive(true);
				return self::$db;
			}else{
				throw new CDbException(Yii::t('yii', 'Unable to connect to DB:[{db}]', array('db', self::$targetDB)));
			}
		}
	}

	public function getDbConnection(){
        return self::getDbInConnection();
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'oc_projects';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('department_id, category_id, user_id, title, purpose, 
				   demands, acceptance, apply_range, verifiers, expecting_date, finished_date, estimated_profit,
				   contact_id, rewards', 'required')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'contact'=>array(
				self::BELONGS_TO,
				'User', 
				'contact_id'
			)//eo contact
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'title' => 'Title'
		);
	}

	public static function model($className=__CLASS__){
    	return parent::model($className);
    }

	public function getFields(){
		return 'Project.id, Project.department_id, Project.dept_path, Project.category_id, Project.user_id, Project.title, Project.status, 
		        Project.purpose, Project.demands, Project.acceptance, Project.apply_range, Project.verifiers, Project.expecting_date,
			    Project.finished_date, Project.rewards, Project.note, Project.sign_proof, 
			    Project.is_published, Project.is_done, Project.is_suspend, Project.is_declined, Project.is_canceled,
			    Project.task_no, Project.contact_id, Project.created, Project.modified';
	}

	public function getOperators(){
		return array('and', 'or', 'like', 'in');
	}

	public function searchByKeyword($keyword, $fields='id', $fetchFields='',$limit=15){
		$fetchFields = $fetchFields === '' ? 'department_id, category_id, user_id, title, purpose, 
				   demands, acceptance, apply_range, verifiers, expecting_date, finished_date,
				   note' : '';
		$fields = explode(',', $fields);
		$keyword = strtr($keyword, array('%'=>'\%', '_'=>'\_', '\\'=>''));
		for($idx=0 ; $idx < count($fields) ; $idx++){
			$where[] = $fields[$idx].'="'.trim($keyword).'"';
		}
		$command = self::$db->createCommand()
							->select($fetchFields)
							->from($this->tableName().' Project')
							->where(implode(' or ', $where))
							->limit($limit);
		return $command->queryAll();
	}

	public function getProjectTypes(){
		$command = self::$db->createCommand()
		                    ->select('id, name, verify_path')
		                    ->from('oc_project_types')
		                    ->where('1')
		                    ->order('id', 'asc');
		return $command->queryAll();
	}

	public function getProjectCategories($departmentId){
		$command = self::$db->createCommand()
							->select('id, department_id, title')
							->from('oc_project_categories Category')
							->where('department_id=:dept_id', array(':dept_id'=>$departmentId));
		return $command->queryAll();
	}

	public function getAll($isDone, $isPublished, $page, $pageSize, $startDate, $endDate, $departmentId, $fromDepartmentId, $sortField, $sortDir, $statusCode, $keywords, $operator='and', $counting=false){

		if(!in_array($operator, $this->getOperators())){
			throw new Expcetion('Operator ['.$operator.'] is not available!');
		}

		$command = self::$db->createCommand();

		$where = array(
			'Project.is_declined=0',
			'Project.is_canceled=0',
		);//conditions
		$params = array();//bind parameters

		if($isDone !== ''){
			$where[] = 'Project.is_done=:is_done';
		    $params[':is_done'] = $isDone;
		}

		if($isPublished){
			$where[] = 'Project.is_published=:is_published';
		    $params[':is_published'] = $isPublished;
		}

		if($startDate !== ''){
			$where[] = 'Project.created>=:start_date';
		    $params[':start_date'] = $startDate;	
		}

		if($endDate !== ''){
			$where[] = 'Project.created>=:end_date';
		    $params[':end_date'] = $endDate;	
		}

		if($departmentId !== ''){
			//find child department ids
			$department = new Department();
			$ids = $department->findKids($departmentId, 4);//4 layers only

			$where[] = 'Project.department_id in ('.implode(',', $ids).')';
		}

		if($fromDepartmentId !== ''){
			$where[] = 'Project.from_department_id=:from_department_id';
		    $params[':from_department_id'] = $fromDepartmentId;	
		}

		if($statusCode !== '' && $statusCode !== 'ALL'){
			$where[] = 'Project.status=:status';
		    $params[':status'] = $statusCode;		
		}


		$where = implode(' '.$operator.' ', $where);

		$command->select($counting ? 'count(*) Count' : $this->getFields().', Department.name department_name, Category.title category_name, Staff.Name contact_name')
				->from($this->tableName().' Project')		
				->rightJoin('oc_departments Department', 'Department.id=Project.department_id')
				->leftJoin('oc_project_categories Category', 'Category.id=Project.category_id')
				->leftJoin('datain_usertable User', 'Project.contact_id=User.Id')
				->leftJoin('datapub_staffmain Staff', 'User.Number=Staff.Number')
			    ->where($where, $params);

		if(!$counting){
			$command->limit($pageSize, ($page*$pageSize));
		}

		if($keywords !== ''){
			$keywords = '%'.strtr($keywords, array('%'=>'\%', '_'=>'\_', '\\'=>'')).'%';
			$command->orWhere(array('like', 'Project.title', $keywords));
			$command->orWhere(array('like', 'Project.purpose', $keywords));
			$command->orWhere(array('like', 'Project.demands', $keywords));
			$command->orWhere(array('like', 'Project.apply_range', $keywords));
			$command->orWhere(array('like', 'Project.acceptance', $keywords));
			$command->orWhere(array('like', 'Category.title', $keywords));
		}

		if($sortField !== ''){
			$command->order($sortField.' '.$sortDir);
		}

		return $counting ? $command->queryScalar() : $command->queryAll();
	}

	public function writeLog($operatorId, $operation, $projectId){
		$command = self::$db->createCommand('INSERT INTO oc_project_logs (id, operator, operation, project_id, created) 
			                                   VALUES(null, :operator, :operation, :project_id, NOW())');
		$command->bindParam(':operator', $operatorId);
		$command->bindParam(':operation', $operation);
		$command->bindParam(':project_id', $projectId);
		return $command->execute();
	}
}