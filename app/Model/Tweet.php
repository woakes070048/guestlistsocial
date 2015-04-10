<?php
class Tweet extends AppModel {
	public $belongsTo = array(
        'TwitterAccount' => array(
            'className' => 'TwitterAccount',
            'foreignKey' => 'account_id'
        )
    );
}