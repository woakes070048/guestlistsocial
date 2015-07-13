<?php
App::uses('Router', 'Routing');

Router::parseExtensions('json');

Configure::write(array(
	'Pusher' => array(
		'credentials' => array(
			'appKey' => '67904c5b4e0608620f41',
			'appSecret' => 'afe96c98ca07900784a4',
			'appId' => '120490'
			//'appKey' => '4eeb1f57466bcd4cc47e',
			//'appSecret' => 'e69dde29e16a8d410fcf',
			//'appId' => '129287'
		),
		'channelAuthEndpoint' => array(
			'plugin' => 'pusher',
			'controller' => 'pusher',
			'action' => 'auth.json',
		)
	)
));

?>