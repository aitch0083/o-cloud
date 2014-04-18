<?php

class Utils {

	const MSG_INFO = 'text-info';
	const MSG_DANGER = 'text-danger';
	const MSF_SUCCESS = 'text-success';
	const LBL_DEFAULT = 'label-default';
	const LBL_INFO = 'label-info';
	const LBL_PRIMARY = 'label-primary';
	const LBL_WARNING = 'label-warning';
	const LBL_DANGER = 'label-danger';

	public static function e($msg, $toEcho=true, $params=null, $yiiCategory='yii'){
		if($toEcho){
			echo Yii::t($yiiCategory, $msg, $params);
		}else{
			return Yii::t($yiiCategory, $msg, $params);
		}
	}

	public static function icon($type, $return=false){
		$icon = '<span class="glyphicon glyphicon-'.$type.'"></span>&nbsp;';
		if($return){
			return $icon;
		}
		echo $icon;
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

	public static function eLabel($msg, $trueFalse, $return=false, $labelForTrue=self::LBL_INFO, $labelForFalse=self::LBL_WARNING){
		$label = '&nbsp;<span class="label '.($trueFalse ? $labelForTrue : $labelForFalse).'">'.self::e($msg, false).'</span>&nbsp;';
		if($return){
			return $label;
		}
		echo $label;
	}

	public static function eBadge($msg, $return=false, $className='badget-default'){
		$badge = '&nbsp;<span class="badge '.$className.'">'.self::e($msg, false).'</span>&nbsp;';

		if($return){
			return $badge;
		}
		echo $badge;
	}	

	public static function encode($text){
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, Yii::app()->params['mcSalt'], $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    public static function decode($text){
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, Yii::app()->params['mcSalt'], base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    public static function buildTree(array &$elements, $parentId = 0) {
	    $branch = array();

	    foreach ($elements as $element) {
	        if ($element['parent_id'] == $parentId) {
	            $children = self::buildTree($elements, $element['id']);
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $branch[$element['id']] = $element;
	        }
	    }
	    return $branch;
	}

	public static function printTree(array &$tree, array &$target, $labelFiled='name', $childrenField='children', $maxLevel=4){
		$currentLevel = 1;

		if($currentLevel > $maxLevel){
			return;
		}

		foreach($tree as $idx=>$node){
			if(in_array($node['id'], $target)){
				echo '<li>'.$node[$labelFiled].'</li>';
				if(isset($node[$childrenField]) && count($node[$childrenField]) > 0){
					self::printTree($node[$childrenField], $target);
				}
			}
		}
	}
}