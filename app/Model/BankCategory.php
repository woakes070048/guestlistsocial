<?php
App::uses('AuthComponent', 'Controller/Component');

class BankCategory extends AppModel {
	public $validate = array(
	    'category' => array(
	            'rule' => array('isUnique', array('category', 'account_id'), false), 
	            'message' => 'Category already exists for this account'
	    )
	);
    public $hasMany = 'TweetBank';
}