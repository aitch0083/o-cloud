<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity{

	private $userRec = null;

	/**
	 * Return authenticated user record.
	 * @return array user record
	*/
	public function getUserRec(){
		return $this->userRec;
	}

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate(){
		$criteria = new CDbCriteria();
		$criteria->condition = 'uName=:username AND uPwd=:password';
		$criteria->params = array(
			':username'=>$this->username,
			':password'=>md5($this->password)
		);
		$user = User::model()->find($criteria);

		if($user){
			$this->userRec = $user;
			$this->setState('user_rec', $user);//write user record to state

			//update login time
			$userModel = new User();
			$userModel->updateLoginTime($user);

			//update online record
			$userModel->updateOnlineRec($user['Id']);

			//get last login time of user
			$lastLoginTime = $userModel->getLastLoginTime($user['Id']);
			$this->setState('last_login_time', $lastLoginTime);

			//get related staff record
			$staffRec = $userModel->getStaffRec($user['Number']);
			$this->setState('staff_record', $staffRec);

			//get related companyGroup
			$companyGroup = $userModel->getLoginFrom();
			$this->setState('company_group', $companyGroup);

			$this->errorCode = self::ERROR_NONE;
		}else{
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}

		return !$this->errorCode;
	}
}