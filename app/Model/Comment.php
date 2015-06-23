<?php
App::uses('AuthComponent', 'Controller/Component');

class Comment extends AppModel {
	public $validate = array(
		'body' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter a comment'
            )
        ),
	);
	public $belongsTo = array(
        'User' => array(
            'className' => 'User'
        )
    );
}