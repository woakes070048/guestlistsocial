<?php
App::uses('Router', 'Routing');

Router::parseExtensions('json');

Configure::write(array(
	'Pusher' => array(
		'credentials' => array(
			'appKey' => '67904c5b4e0608620f41',
			'appSecret' => 'afe96c98ca07900784a4',
			'appId' => '120490'
		),
		'channelAuthEndpoint' => array(
			'plugin' => 'pusher',
			'controller' => 'pusher',
			'action' => 'auth.json',
		)
	)
));

?>