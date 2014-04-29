<?php

class Task extends CActiveRecord{

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
		return 'oc_project_tasks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, user_id, category, content, expecting_date, in_charge, in_charge_names, currency_type, budget', 'required')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
		);
	}

	public static function model($className=__CLASS__){
    	return parent::model($className);
    }

	public function getFields(){
		return 'Task.id, Task.project_id, Task.user_id, Task.category, Task.content, Task.expecting_date, Task.budget, Task.currency_type,
			    Task.finish_date, Task.file, Task.in_charge, Task.in_charge_names, Task.created, Task.modified';
	}

	public function create($projectId, $userId, 
						   array $descriptions, array $duedates, array $responsibles, array $responsibleIds, array $deliverables,
						   array $files, array $budgets, array $currentTypes){

		$connection = self::$db;
		$transaction = $connection->beginTransaction();
		try{
			$sql = 'INSERT INTO '.$this->tableName().' (id, project_id, user_id, category, content, deliverable, expecting_date, finished_date, file, in_charge, in_charge_names, budget, currency_type, created, modified) VALUES';
			for($idx = 0 ; $idx < count($descriptions) ; $idx++){
				$fileUrl = '';
				if(!$files['error']['files'][$idx] && $files['name']['files'][$idx] !== ''){
					$fileUrl = $this->uploadFile($files['name']['files'][$idx], $files['tmp_name']['files'][$idx]);
				}else{
					$fileUrl = '';
				}
				$sql .= '(null, '.$projectId.', '.$userId.', "TODO", "'.$descriptions[$idx].'", "'.$deliverables[$idx].'", "'.
								  $duedates[$idx].'", "0000-00-00", "'.$fileUrl.'", "'.$responsibleIds[$idx].'", "'.
								  $responsibles[$idx].'", "'.$budgets[$idx].'", "'.$currentTypes[$idx].'", NOW(), NOW()),';
			}
			$sql = mb_substr($sql, 0, -1, 'utf-8');
			$connection->createCommand($sql)->execute();
			$transaction->commit();
			return true;
		}catch(Exception $exp){
			$transaction->rollback();
			return false;
		}
	}

	public function read($projectId){
		$command = self::$db->createCommand()
							->select()
							->from($this->tableName())
							->where('project_id=:project_id', array(':project_id'=>$projectId));
		return $command->queryAll();
	}

	public function updateFile($file, $id){
		if(isset($file['error']) && !$file['error']){
			$newFilename = $this->uploadFile($file['name'], $file['tmp_name']);
			$task = self::model()->findByPk($id);
			$task->file = $newFilename;
			return $task->save();
		}
		return false;
	}

	public function updateField($field, $value, $id){
		$task = self::model()->findByPk($id);
		$task->$field = $value;
		return $task->save();
	}

	public function getTaskNo($projectId){
		$command = self::$db->createCommand()->select('COUNT(*) count')->from($this->tableName())->where('project_id=:project_id', array(':project_id'=>$projectId));
		return $command->queryScalar();
	}

	private function uploadFile($filename, $tmpname){

		if($filename === ''){
			return '';
		}

		$uploadFolder = Yii::app()->basePath.'/../files/task_files/';
		
		$name = md5(rand(100, 200));
        $ext = explode('.', strtolower($filename));
        $filename = $name . '.' . array_pop($ext);
        $destination = $uploadFolder . $filename; 
        $location = $tmpname;
        move_uploaded_file($location, $destination);

        return '/files/task_files/' . $filename;//change this UR
	}
	
}