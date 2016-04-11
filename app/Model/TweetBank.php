<?php

App::uses('AuthComponent', 'Controller/Component');


class TweetBank extends AppModel {
	public $validate = array(
	    'body' => array(
	            'rule' => array('isUnique', array('body', 'bank_category_id', 'img_url'), false), 
	            'message' => 'Tweet already exists for this account and category'

	    )
	);


    public $belongsTo = array(
        'BankCategory' => array(
            'className' => 'BankCategory',
            'foreignKey' => 'bank_category_id'
        )
    );


    public $hasMany = 'Tweet';

    public $actsAs = array('Containable');
}