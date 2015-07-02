<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
        'DebugKit.Toolbar',
        'Acl',
       	'Session',
        'Auth' => array(
        	'authenticate' => array('Form' => array( 'userModel' => 'User',
                                    'fields' => array('username' => 'email',
                                                      'password' => 'password'))),
            'loginRedirect' => array('controller' => 'twitter', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'twitter', 'action' => 'index'),
            'authorize' => array('Actions' => array('actionPath' => 'controllers'))
            //'authorize' => array('Controller')
        ));


    public function beforeFilter() {
        $this->Auth->allow('login', 'register', 'logout', 'forgotpw', 'resetpw');
        $this->loadModel('Notification');
        $notificationCount = $this->Notification->find('count', array('conditions' => array('user_id' => $this->Session->read('Auth.User.id'), 'read' => 0)));
        $this->set('notificationCount', $notificationCount);
    }

    
    public function isAuthorized() {
        if ($this->Session->read('Auth.User.session_id')) {
            return true;
        } else

        return false;
    
    }

    public function refreshGroup($user_id) {
        $user = $this->User->find('all', array('conditions' => array('User.id' => $user_id)));

        foreach ($user[0]['Team'] as $key) {
            $groups[] = $key['TeamsUser']['group_id'];
        }

        $this->User->id = $user[0]['User']['id'];

        if (in_array(1, $groups)) {
            if ($this->User->saveField('group_id', 1)) {
                return true;
            }
        } elseif (in_array(7, $groups)) {
            if ($this->User->saveField('group_id', 7)) {
                return true;
            }
        } elseif (in_array(2, $groups)) {
            if ($this->User->saveField('group_id', 2)) {
                return true;
            }
        } elseif (in_array(0, $groups)) {
            if ($this->User->saveField('group_id', 2)) {
                return true;
            }
        }

        return false;

    }
}