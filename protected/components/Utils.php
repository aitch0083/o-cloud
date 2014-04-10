<?php

class Utils {

	const MSG_INFO = 'text-info';
	const MSG_DANGER = 'text-danger';
	const MSF_SUCCESS = 'text-success';

	public static function e($msg, $toEcho=true, $params=null, $yiiCategory='yii'){
		if($toEcho){
			echo Yii::t($yiiCategory, $msg, $params);
		}else{
			return Yii::t($yiiCategory, $msg, $params);
		}
	}

	public static function icon($type){
		echo '<span class="glyphicon glyphicon-'.$type.'"></span>&nbsp;';
	}

	public static function msg($type=self::MSG_INFO, $msg='', $toEcho=true, $class=''){
		if($toEcho){
			echo '<div class="'.$type.' '.$class.'">'.self::e($msg, false).'</div>';
		}else{
			return '<div class="'.$type.' '.$class.'">'.self::e($msg, false).'</div>';
		}
	}

	public static function dump($records, $isHtml=true){
		if($isHtml){
			echo '<pre>'.CVarDumper::dump($records).'</pre>';
		}else{
			return CVarDumper::dumpAsString($records);
		}
	}
}