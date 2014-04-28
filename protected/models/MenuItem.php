<?php 


class MenuItem extends CActiveRecord{
	
	public static $db = null;
	private static $targetDB = 'db';

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
		return 'datapub_funmodule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
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

	public function getMenuItems($uId, $menuTypeId=1, $findKids=false, $companyGroup = null){
		if($menuTypeId <= 0){
			throw new Exception('Error menu level type! Cannot be less or equal to 0.');
		}else if($findKids && $companyGroup === null){
			throw new Exception('Company record must be assigned when looking for the kids!');
		}
		//cross database join
		$command = self::$db->createCommand('SELECT A.ModuleId, B.ModuleName AS label
					FROM datain_upopedom A 
					LEFT JOIN datapub_funmodule B ON B.ModuleId=A.ModuleId 
					WHERE 1 AND A.Action>0 AND B.TypeId='.$menuTypeId.' AND A.UserId=:uId AND B.Estate=1 ORDER BY B.OrderId');
		
		$command->bindParam(':uId', $uId);

		$items = $command->queryAll();

		if($findKids === false){//no finding kids, good, off work~
			return $items;
		}else{//otherwise, do the job
			$cSign = $companyGroup['companyDomain']['cSign'];
			foreach($items as $idx=>$item){
				//cross database query
				$command = self::$db->createCommand('SELECT F.ModuleId 
					FROM datapub_modulenexus M 
					LEFT JOIN datain_upopedom U ON U.ModuleId =M.dModuleId 
					LEFT JOIN datapub_funmodule F ON F.ModuleId=U.ModuleId 
					WHERE U.UserId ='.$uId.' and M.ModuleId='.$item['ModuleId'].' and U.Action>0 and F.Estate=1  and (F.cSign=0 or F.cSign=:cSign) 
					LIMIT 1');
				$command->bindValue(':cSign', $cSign);
				$subMenuItems = $command->queryRow();
				if($subMenuItems){
					$items[$idx]['children'] = $subMenuItems;
				}
			}

			return $items;
		}
	}

	
}