<?php
App::uses('AuthComponent', 'Controller/Component');

class Editor extends AppModel {
	public $belongsTo = array(
        'Tweet' => array(
            'className' => 'Tweet'
        ),
        'User' => array(
        	'className' => 'User'
        )
    );
}