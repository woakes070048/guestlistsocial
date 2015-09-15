<?php
class FirstLoginsController extends AppController {
	public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form', 'Session');

    public function first() {
    	$this->layout = 'firstLogin';
    	if ($this->FirstLogin->isFirstLogin()) {
    		if (!empty($this->FirstLogin->getStage())) {
    			debug('ok');
    		} else {//redirect to home page

    		}
    	} else {//redirect to home page

    	}
    }
}