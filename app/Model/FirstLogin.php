<?php
App::uses('AuthComponent', 'Controller/Component');

class FirstLogin extends AppModel {
    public $belongsTo = 'User';

    public function isFirstLogin() {
    	App::uses('SessionComponent', 'Controller/Component'); 
    	$user_id = SessionComponent::read('Auth.User.id');

    	//1 = not first login, 0 = first login(or uncompleted first login setup pages)
    	$isFirstLogin = $this->User->find('first', array('fields' => array('first_login_complete'), 'conditions' => array('User.id' => SessionComponent::read('Auth.User.id')), 'recursive' => -1));

    	if ($isFirstLogin['User']['first_login_complete'] == 0) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function getStage() {
    	$stage = $this->find('first', array('conditions' => array('user_id' => SessionComponent::read('Auth.User.id'))));
    	if (!empty($stage)) {
    		return $stage['FirstLogin']['stage'];
    	} else {
    		return false;
    	}
    }
}