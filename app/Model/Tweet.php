<?php
class Tweet extends AppModel {
	public $belongsTo = array(
        'TwitterAccount' => array(
            'className' => 'TwitterAccount',
            'foreignKey' => 'account_id'
        )
    );

    public $hasMany = array(
   		'Comment' => array(
    		'className' => 'Comment'
    	),
    	'Editor' => array(
    		'className' => 'Editor',
    		'order' => 'Editor.created ASC'
    	)
    );
}