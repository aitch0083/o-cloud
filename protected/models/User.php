<?php
/**
 * This is the model class for User, which supports cross db query.
 * 
 * @author Aitch <aitch@ozaki.com.tw>
 * @version 0.0.1
 * The following columns are available in User model:
 * @property integer $id
 * @property integer $uType
 * @property string $uName
 * @property string $uPwd
 * @property string $Number
 * @property integer $uSeal
 * @property string $IDate
 * @property string $Date
 * @property integer $WebStyle
 * @property string $FaxNO
 * @property inetger $uSign
 * @property integer $Estate
 * @property integer $Locks
 * @property integer $Operator
 */
class User extends CActiveRecord{

	private static $dbIn = null;
	private static $targetDB = 'dbIn';

	/**
	 * @return resource target database conection
	*/
	protected static function getDbInConnection(){
		if(self::$dbIn !== null){
			return self::$dbIn;
		}else{
			self::$dbIn = Yii::app()->ocdb;
			if(self::$dbIn instanceof CDbConnection){
				self::$dbIn->setActive(true);
				return self::$dbIn;
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
		return 'datain_usertable';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('uName, uPwd', 'required'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'uName' => Yii::t('yii', 'Username'),
			'uPwd' => Yii::t('yii', 'Password')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'staff'=>array(
				self::HAS_ONE,
				'Staff', 
				array('Number'=>'Number')
			)//eo contact
		);
	}

	public function getLoginFrom(){

		//find company's In/Out domain
		$connection = Yii::app()->db;
		$command = $connection->createCommand('SELECT cSign, EShortName, CShortName, OutIp FROM  datapub_companys_group WHERE Db=:dataIn LIMIT 1');
		$command->bindValue(':dataIn', 'd7', PDO::PARAM_STR);//d7 is the database for input data
		$companyDoamin = $command->queryRow();
		$outIp = $companyDoamin['OutIp'];
		$loginIp = Yii::app()->request->getUserHostAddress();

		//check where user login from: $ufrom
		$uFrom = 0;
		if(preg_match('/^192\.168/',$loginIp) || preg_match('/^172\.168/',$loginIp)){
			$uFrom = 1; //login from internal address
		}else if($loginIp === $outIp){
			$uFrom = 2; //login from company's domain
		}else{
			$uFrom = 3; //login from outside
		}

		return array(
			'uFrom'=>$uFrom,
			'companyDomain'=>$companyDoamin
		);
	}

	public function updateLoginTime($userRec, $datetime=''){
		$datetime = $datetime === '' ? date('Y-m-d H:i:s') : $datetime;

		//find company's In/Out domain
		$loginIp = Yii::app()->request->getUserHostAddress();
		$companyGroup = $this->getLoginFrom();
		$uFrom = $companyGroup['uFrom'];

		$connection = Yii::app()->dbIn;
		$command = $connection->createCommand('INSERT INTO datain_loginlog (Id, uId, uType, uName, uFrom, uIP, sTime, eTime) VALUES (NULL, :uid, :utype, :uname, :ufrom, :uip, :stime, "0000-00-00 00:00:00")');
		$command->bindValue(':uid', $userRec['Id'], PDO::PARAM_INT);
		$command->bindValue(':utype', $userRec['uType'], PDO::PARAM_STR);
		$command->bindValue(':uname', $userRec['uName'], PDO::PARAM_STR);
		$command->bindParam(':ufrom', $uFrom, PDO::PARAM_STR);
		$command->bindParam(':uip', $loginIp, PDO::PARAM_STR);
		$command->bindParam(':stime', $datetime, PDO::PARAM_STR);

		return $command->execute();
	}

	public function getLastLoginTime($uId){
		$connection = Yii::app()->dbIn;
		$command = $connection->createCommand('SELECT eTime FROM datain_loginlog WHERE uId=:uid ORDER BY id DESC LIMIT 1');
		$command->bindParam(':uid', $uId, PDO::PARAM_INT);
		$result = $command->queryRow();

		return $result['eTime'];
	}

	public function getStaffRec($uNumber){
		$connection = Yii::app()->ocdb;
		$command = $connection->createCommand('SELECT 
			A.Id, A.Name, A.ExtNo, A.BranchId, A.auth_code, B.Name AS Branch, C.WorkNote, C.WorkTime, D.CShortName 
			FROM datapub_staffmain A
			LEFT JOIN oc_departments B ON B.id=A.BranchId 
			LEFT JOIN datapub_jobdata C ON C.Id=A.JobId 
			LEFT JOIN datapub_companys_group D ON D.cSign=A.cSign
			WHERE A.Number=:unumber ORDER BY A.Id LIMIT 1');
		$command->bindParam('unumber', $uNumber, PDO::PARAM_INT);
		return $command->queryRow();
	}

	public function updateOnlineRec($uId){
		$loginIp = Yii::app()->request->getUserHostAddress();
		$companyGroup = $this->getLoginFrom();
		$uFrom = $companyGroup['uFrom'];

		$connection = Yii::app()->dbIn;
		$command = $connection->createCommand('INSERT INTO datain_online (sId, uId, uFrom, IP, LastTime) VALUES (NULL, :uid, :ufrom, :loginip, :time)');
		$command->bindParam(':uid', $uId, PDO::PARAM_INT);
		$command->bindParam(':ufrom', $uFrom, PDO::PARAM_STR);
		$command->bindParam(':loginip', $loginIp, PDO::PARAM_STR);
		$command->bindValue(':time', time(), PDO::PARAM_INT);

		return $command->execute();
	}

	public function getAllUsers(){
		$command = self::$dbIn->createCommand('SELECT 
			A.Id, A.Name, A.Nickname, A.ExtNo, A.BranchId, A.auth_code, A.Mail, 
			B.Name AS Branch, 
			C.WorkNote, C.WorkTime, C.Name AS Title, 
			D.CShortName,
			Department.name AS LeadDept,
			Department.id AS LeadDeptId,
			DepartmentContact.name AS ContactDept,
			DepartmentContact.id AS ContactDeptId
			FROM datapub_staffmain A
			INNER JOIN oc_departments B ON B.id=A.BranchId 
			INNER JOIN datapub_jobdata C ON C.Id=A.JobId 
			INNER JOIN datapub_companys_group D ON D.cSign=A.cSign
			LEFT JOIN oc_department_leaders Leader ON A.Id=Leader.user_id
			LEFT JOIN oc_departments Department ON Leader.department_id=Department.id
			LEFT JOIN oc_department_contacts Contact ON A.Id=Contact.user_id
			LEFT JOIN oc_departments DepartmentContact ON Contact.department_id=DepartmentContact.id
			WHERE 1 ORDER BY A.Id DESC LIMIT 300');

		return $command->queryAll();
	}

	public function updateField($field, $value, $id){
		switch($field){
			case 'Mail':
				$sql = 'UPDATE datapub_staffmain SET Mail=:mail WHERE datapub_staffmain.Id=:id';
				$command = self::$dbIn->createCommand($sql);
				$command->bindParam(':mail', $value);
				$command->bindParam(':id', $id);
				return $command->execute();
				break;
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TblUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}