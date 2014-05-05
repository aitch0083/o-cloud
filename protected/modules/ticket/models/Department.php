<?php

class Department extends CActiveRecord{

	public static $db = null;
	private static $targetDB = 'ocdb';
        public $name = '';

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
	 * @return string associated table name
	 */
	public function tableName(){
		return 'oc_departments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('country, city, name, parent_id, level', 'required'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'country' => Yii::t('yii', '國家'),
			'city' => Yii::t('yii', '城市'),
			'name' => Yii::t('yii', '名稱'),
		);
	}

	public function getAll(){
		$command = self::$db->createCommand()->select('id, parent_id, name, is_open, level')->from($this->tableName());

		return $command->queryAll();
	}

	public function getMaxLevel(){
		$command = self::$db->createCommand()->select('MAX(level) AS MAX_LEVEL')
											 ->from($this->tableName());
		return $command->queryScalar();											 
	}

	public function getList($level, $parentId){
		$command = self::$db->createCommand()->select('id, parent_id, level, name, is_open')
											 ->from($this->tableName())
											 ->where('level=:level and parent_id=:parent_id', array(':level'=>$level,
											 														':parent_id'=>$parentId));
		$records = $command->queryAll();
		return $records;											 
	}

	public function getOpendList(){
		$command = self::$db->createCommand()->select('id, parent_id, level, name, is_open')
											 ->from($this->tableName())
											 ->where('is_open=1');
		$records = $command->queryAll();
		return $records;											 	
	}

	public function getContact($departmentId, $limit=1){
		$command = self::$db->createCommand()->select('job.Name title, user.Id, user.Name, user.Mail, user.ExtNo')
											 ->from('oc_department_contacts dc_pivot')		
											 ->join('oc_departments department', 'dc_pivot.department_id=department.id')
											 ->join('datapub_staffmain user', 'dc_pivot.user_id=user.Id')
											 ->join('datapub_jobdata job', 'user.JobId=job.Id')
											 ->where('dc_pivot.department_id=:dept_id', array(':dept_id'=>$departmentId))
											 ->limit($limit);
		return $command->queryRow();
	}

	public function getLeader($departmentId, $limit=1){
		$command = self::$db->createCommand()->select('user.Id')
											 ->from('oc_department_leaders dc_pivot')		
											 ->join('oc_departments department', 'dc_pivot.department_id=department.id')
											 ->join('datapub_staffmain user', 'dc_pivot.user_id=user.Id')
											 ->where('dc_pivot.department_id=:dept_id', array(':dept_id'=>$departmentId))
											 ->limit($limit);
		return $command->queryRow();	
	}

	public function assignLeader($departmentId, $userId, $operation='assign_leader'){
		$taregtTable = ($operation === 'assign_leader' ? 'oc_department_leaders' : 'oc_department_contacts');
		//find duplicate fisrt
		$command = self::$db->createCommand('SELECT id FROM '.$taregtTable.' Leader WHERE Leader.department_id=:deptId AND Leader.user_id=:userId LIMIT 1');
		$command->bindParam(':deptId', $departmentId);
		$command->bindParam(':userId', $userId);
		if($command->queryRow()){//return 
			return self::$db->createCommand('UPDATE `datapub_staffmain` SET `auth_code`=7 WHERE `Id`=:Id')->bindValue(':Id', $userId)->execute();
		}else{//create one
			//remove first, only one can be the leader/contact
			self::$db->createCommand()->delete($taregtTable, 'department_id=:deptId', array(':deptId'=>$departmentId));
			//then create 
			$command = self::$db->createCommand('INSERT INTO '.$taregtTable.' (id, department_id, user_id, created, modified) VALUES(null, :deptId, :userId, NOW(), NOW())');
			$command->bindParam(':deptId', $departmentId);
			$command->bindParam(':userId', $userId);
			return $command->execute();
		}
	}

	public function removeLeader($departmentId, $userId, $operation='remove_leader'){
		$taregtTable = ($operation === 'remove_leader' ? 'oc_department_leaders' : 'oc_department_contacts');
		$result = self::$db->createCommand()->delete($taregtTable, 'department_id=:deptId and user_id=:userId', array(':deptId'=>$departmentId, ':userId'=>$userId));
		if($result){
			self::$db->createCommand('UPDATE `datapub_staffmain` SET `auth_code`=7 WHERE `Id`=:Id')->bindValue(':Id', $userId)->execute();
		}

		return $result;
	}	

	public function isOpen($departmentId){
		$command = self::$db->createCommand()
							->select('is_open')
							->from($this->tableName())
							->where('id=:dept_id', array(':dept_id'=>$departmentId))
							->limit(1);
		return $command->queryScalar();
	}

	public function findBIs($branchId){
		$command = self::$db->createCommand()->select()->from('oc_project_categories')->where('department_id=:dept_id', array(':dept_id'=>$branchId));
		return $command->queryAll();
	}

	public function findKids($departmentId, $maxLayer=5){
		$realMaxLevel = $this->getMaxLevel();
		$maxLayer = $maxLayer >= $realMaxLevel ? $realMaxLevel : $maxLayer;

		$command = self::$db->createCommand('SELECT level FROM '.$this->tableName().' Department WHERE id IN ('.(is_array($departmentId) ? implode(',', $departmentId) : $departmentId).') LIMIT 1');
		$deptLevel = $command->queryScalar();

		$layersToGo = $maxLayer - $deptLevel;
		
		if($layersToGo <= 0){
			return array($departmentId);
		}else{
			$command = self::$db->createCommand('SELECT id, level FROM '.$this->tableName().' Department WHERE parent_id=:id');
			$command->bindValue(':id', (is_array($departmentId) ? implode(',', $departmentId) : $departmentId));
			$kids = $command->queryAll();
			if($kids){
				for($idx = 0 ; $idx < count($kids) ; $idx++){
					$grandKids = $this->findKids( $kids[$idx], $maxLayer );
					$kids[$idx] =  $grandKids ? array_push($grandKids, $kids[$idx]) : array($kids[$idx]);
				}
			}

			return $kids ? $kids : array($departmentId);
		}
	}

	public function addBizItem($deptId, $userId, $title){
		$command = self::$db->createCommand('INSERT INTO `oc_project_categories` (id, user_id, department_id, title, created, modified) VALUES(null, :userId, :deptId, :title, NOW(), NOW())');
		$command->bindParam(':deptId', $deptId);
		$command->bindParam(':userId', $userId);
		$command->bindParam(':title', $title);
		if($command->execute()){
			return Yii::app()->ocdb->getLastInsertID();
		}

		return false;
	}

	public function editBizItem($pk, $userId, $title){
		$command = self::$db->createCommand('UPDATE `oc_project_categories` SET title=:title, user_id=:userId, modified=NOW() WHERE id=:id');
		$command->bindParam(':userId', $userId);
		$command->bindParam(':title', $title);
		$command->bindParam(':id', $pk);

		return $command->execute();
	}

	public function delBizItem($pk){
		$command = self::$db->createCommand('DELETE FROM `oc_project_categories` WHERE `id`=:pk');
		$command->bindParam(':pk', $pk);

		return $command->execute();
	}

}
