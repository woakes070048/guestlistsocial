<?php
App::uses('AuthComponent', 'Controller/Component');
class Team extends AppModel {

	public $hasAndBelongsToMany = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}