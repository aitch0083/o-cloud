<?php

class Project extends CActiveRecord{

	public static $db = null;
	private static $targetDB = 'ocdb';

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
				throw new CDbException(Yii::t('yii', 'Unable to connect to DB:['.self::$targetDB.']'));
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
				   demands, acceptance, apply_range, verifiers, expecting_date, finished_date,
				   note, contact_id, rewards', 'required')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
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

	public function getFields(){
		return 'Project.id, Project.department_id, Project.category_id, Project.user_id, Project.title, Project.purpose, Project.demands, 
		        Project.acceptance, Project.apply_range, Project.verifiers, Project.expecting_date,
			    Project.finished_date, Project.rewards, Project.note, Project.is_published, Project.is_done, Project.sign_proof, 
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

	public function getProjectCategories($departmentId){
		$command = self::$db->createCommand()
							->select('id, department_id, title')
							->from('oc_project_categories Category')
							->where('department_id=:dept_id', array(':dept_id'=>$departmentId));
		return $command->queryAll();
	}

	public function getAll($isDone, $isPublished, $page, $pageSize, $startDate, $endDate, $departmentId, $fromDepartmentId, $sortField, $sortDir, $keywords, $operator='and', $counting=false){

		if(!in_array($operator, $this->getOperators())){
			throw new Expcetion('Operator ['.$operator.'] is not available!');
		}

		$command = self::$db->createCommand();

		$where = array();//conditions
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
}