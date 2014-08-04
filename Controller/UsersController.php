<?php 
class UsersController extends AppController {
    public $helpers = array('Html','Form');
    public $components = array('Tickets');
    var $uses = array('Team', 'User', 'Ticket');

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow(array('action' => 'logout'));
		$this->Auth->allow('initDB', 'verify');
    }

	public function register() {
	        if ($this->request->is('post')) {
                $hash=sha1($this->request->data['User']['first_name'].rand(0,100));
                $this->User->data['User']['registration_hash'] = $hash;
	            if ($this->User->save($this->request->data)) {
                    debug($this->request->data);
	            	$this->User->saveField('session_id', $this->Session->id());
	            	$this->User->saveField('group_id', 6);
                    $id = $this->User->getLastInsertId();
                        if (isset($this->passedArgs['h'])) {//adding to team if invited
                            $teamhash = $this->passedArgs['h'];
                            $team = $this->Team->find('all', array('fields' => array('id', 'name', 'hash'), 'conditions' => array('hash' => $teamhash)));
                            $this->User->id = $id;
                            $this->User->saveField('team_id', $team[0]['Team']['id']);
                            $calendar_activated = $this->User->find('first', array('fields' => 'calendar_activated', 'conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'), 'group_id' => 1)));
                            $this->User->saveField('calendar_activated', $calendar_activated['User']['calendar_activated']);
                            $this->Session->setFlash('You have successfully been registered and added to team ' . $team[0]['Team']['name'] . '. Please log in. Note: You will not have access to any of your team\'s twitter accounts until the team admin gives you permissions');
                            $this->redirect(array('controller' => 'users', 'action' => 'login'));
                        }
                    $msg = "Please click on the link below to activate you account with Guestlist Social:

                    " . Router::url(array('action' => 'verify', 'id' => $id, 'h' => $hash), true);
                    $Email = new CakeEmail();
                    $Email->from(array('registration@social.guestlist.net' => 'Guestlist Social'));
                    $Email->to($this->request->data['User']['email']);
                    $Email->subject('Confirm Registration for Guestlist Social');
                    $Email->send($msg);
	                $this->Session->setFlash(__('Please check your email to complete registration.'));
	                $this->redirect(array('controller' => 'users', 'action' => 'login'));
	            } else {
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }

	        }
	}

    public function verify() {
        if (!empty($this->passedArgs['id']) && !empty($this->passedArgs['h'])){
            $id = $this->passedArgs['id'];
            $hash = $this->passedArgs['h'];
            $results = $this->User->find('all', array('fields' => array('group_id', 'registration_hash'), 'conditions' => array('User.id' => $id)));
            //check if the user is already activated
            if ($results[0]['User']['group_id'] == 6) {
            //check the token
                if($results[0]['User']['registration_hash'] == $hash) {
                    debug($id);
                    $this->User->id = $id;
                    $this->User->saveField('group_id', 2);
                    $this->Session->setFlash('Your registration is complete. Please log in.');
                    $this->redirect('/users/login');
                    exit;
                } else { //hashes don't match
                    $this->Session->setFlash('Your registration failed please try again');
                    $this->redirect('/users/register');
                }
            } else { // activated = 1
                $this->Session->setFlash('Token has alredy been used');
                $this->redirect('/users/register');
            }
        } else { //empty arguments
            $this->Session->setFlash('Token corrupted. Please re-register');
            $this->redirect('/users/register');
        }
            
    } 

	public function login() {
    	if ($this->request->is('post')) {
        	if ($this->Auth->login()) {
        	 $this->User->id = $this->Session->read('Auth.User.id');
	         $this->User->saveField('session_id', $this->Session->id());
           	 $this->redirect($this->Session->read('Auth.redirect'));
      	  } else {
           	 $this->Session->setFlash(__('Invalid username or password, try again'));
        	}
    	} else {
            
        }

        $this->layout = 'loginlayout';
	}

	public function logout() {
	 $this->Session->destroy();
   	 $this->redirect($this->Auth->logout());
	}

    public function forgotpw() {
        if ($this->request->data) {
            $user = $this->User->find('first', array('conditions' => array('email' => $this->request->data['User']['email'])));
            if ($user) {
                $msg = "We have recieved a request to reset your password at Guestlist Social. If this request was not made by you, please ignore this email.
                If you would like to reset you password, please click the link below:
                " . Router::url(array('action' => 'resetpw', $this->Tickets->set($user['User']['email'])), true);

                $Email = new CakeEmail();
                $Email->from(array('admin@social.guestlist.net' => 'Guestlist Social'));
                $Email->to($this->request->data['User']['email']);
                $Email->subject('Password Reset');
                $Email->send($msg);
                $this->Session->setFlash(__('An e-mail has been sent to the address given. Please follow the link in the e-mail to reset your password.'));
            } else {
                $this->Session->setFlash(__('User not found in database. Please register from the homepage.'));
            }
        }

        $this->layout = 'loginlayout';
    }

    public function resetpw ($hash = null) {
        if ($email = $this->Tickets->get($hash)) {
            if ($user = $this->User->find('first', array('conditions' => array('email' => $email)))) {
                if ($this->request->data) {
                    debug($this->request->data);
                    $this->User->id = $user['User']['id'];
                    if ($this->request->data['User']['password'] === $this->request->data['User']['password2']) {
                        if ($this->User->save($this->request->data)) {
                            $this->Session->setFlash(__('Password Reset'));
                            $this->Tickets->del($hash);
                            $this->redirect('/');
                        }
                    } else {
                        $this->Session->setFlash(__('Passwords do not match'));
                    }
                }
            }
        }

        $this->layout = 'loginlayout';
    }

    public function initDB() {
    /*
    Group id's and names
    1 => administrators
    2 => team_members
    5 =>
    6 => not_activated
    7 => proofers
    */

    $group = $this->User->Group;

    // Allow admins to everything
    //$group->id = 1;
    //$this->Acl->allow($group, 'controllers/teams/manageteam');

    // allow managers to posts and widgets
    //$group->id = 2;
    //$this->Acl->allow($group, 'controllers/teams/manage');

    //$group->id = 5;
    //$this->Acl->allow($group, 'controllers');
    //$this->Acl->allow($group, 'controllers/teams/manageteam');

    // allow basic users to log out
    //$this->Acl->allow($group, 'controllers/users/logout');

    $group->id = 1;
    $this->Acl->allow($group, 'controllers/teams/permissionSave');
    $this->Acl->allow($group, 'controllers/teams/removeFromTeam');
    //$group->id = 2;
    //$this->Acl->allow($group, 'controllers/teams/addtoTeam');
    $group->id = 5;
    $this->Acl->allow($group, 'controllers/teams/permissionSave');
    $this->Acl->allow($group, 'controllers/teams/removeFromTeam');
    //$group->id = 7;
    //$this->Acl->allow($group, 'controllers/teams/addtoTeam');
    // we add an exit to avoid an ugly "missing views" error message
    echo "all done";
    exit;
}
}