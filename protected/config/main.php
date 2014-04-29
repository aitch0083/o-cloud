<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('widgets', dirname(__FILE__).'/../views/widgets');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'O!Cloud',
    'timeZone'=>'Asia/Taipei',
    'language'=>'zh_tw',
    'sourceLanguage'=>'zh_tw',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*'
	),

	'theme'=>'classic',

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'yiiweb',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array($_SERVER['REMOTE_ADDR'])
		),
		'ticket'=>array(
			'class'=>'application.modules.ticket.TicketModule'
		)
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'clientScript'=>array(
            'packages'=>array(
                'jquery'=>array(
                    'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jquery/1/',
                    'js'=>array('jquery.min.js'),
                )
            ),
        ),
		//'db'=>array(
		//	'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		//),
		// uncomment the following to use a MySQL database
		'db'=>array( //data_public <=> d0, [DEFAULT]
			'connectionString' => 'mysql:host=localhost;dbname=oc_db',
			'emulatePrepare' => true,
			'username' => 'yii_web',
			'password' => 'yii_webpass_20140317',
			'charset' => 'utf8',
			'enableProfiling' => true
		),
		'dbIn'=>array(//data_in <=> d7
			'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=oc_db',
			'emulatePrepare' => true,
			'username' => 'yii_web',
			'password' => 'yii_webpass_20140317',
			'charset' => 'utf8',
			'enableProfiling' => true
		),
		'dbOut'=>array(//data_out <=> d3
			'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=oc_db',
			'emulatePrepare' => true,
			'username' => 'yii_web',
			'password' => 'yii_webpass_20140317',
			'charset' => 'utf8',
			'enableProfiling' => true
		),
		'ocdb'=>array(//data_out <=> d3
			'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=oc_db',
			'emulatePrepare' => true,
			'username' => 'yii_web',
			'password' => 'yii_webpass_20140317',
			'charset' => 'utf8',
			'enableProfiling' => true
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*array(
					'class'=>'CWebLogRoute',
				),
				array(
					'class'=>'CProfileLogRoute',
                    'report'=>'summary'
				)*/
			),
		),
		'messages' => array(
           'class' => 'CPhpMessageSource',
           'onMissingTranslation' => array('Ei18n', 'missingTranslation')
        ),
        'Smtpmail'=>array(
            'class'=>'application.extensions.smtpmail.PHPMailer',
            'Host'=>"ozaki.com.tw",
            'Username'=>'aitch@ozaki.com.tw',
            'Password'=>'Jek0375abd',
            'Mailer'=>'smtp',
            'Port'=>587,
            'SMTPAuth'=>true,
            'SMTPSecure' => 'tls',
        )
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'aitch@ozaki.com.tw',
		'mcSalt'=>'OzakiOCloud20140409McCryptSalt'
	),
);
