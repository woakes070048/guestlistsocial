<?php 
class UsersController extends AppController {
    public $helpers = array('Html','Form');
    public $components = array('Tickets');
    var $uses = array('Team', 'User', 'Ticket', 'TeamsUser', 'Group');

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
                            $teamHash = $this->passedArgs['h'];
                            $team = $this->Team->find('first', array('fields' => array('id', 'name', 'hash'), 'conditions' => array('id' => $this->Tickets->get($teamHash))));
                            if (isset($this->passedArgs['g'])) {
                                $group = $this->Tickets->get($this->passedArgs['g']);
                            } else {
                                $group = 2;
                            }
                            $save = array(
                                    'TeamsUser' => array (
                                        'user_id' => $id,
                                        'team_id' => $this->Tickets->get($teamHash),
                                        'group_id' => $group
                                    )
                                );
                            $this->TeamsUser->save($save);
                            $calendar_activated = $this->User->Team->find('all', array('conditions' => array('Team.id' => $this->Tickets->get($teamHash))));
                            $this->User->saveField('calendar_activated', $calendar_activated[0]['User'][0]['calendar_activated']);
                            $this->User->saveField('group_id', $group);
                            $this->User->saveField('profile_pic', '/img/profile/defualt'. rand(0,5) . '.jpg');
                            $this->Session->setFlash('You have successfully been registered and added to team ' . $team[0]['Team']['name'] . '. Please log in. Note: You will not have access to any of your team\'s twitter accounts until the team admin gives you permissions');
                            $this->Tickets->del($this->passedArgs['h']);
                            $this->Tickets->del($this->passedArgs['g']);
                            $this->redirect(array('controller' => 'users', 'action' => 'login'));
                        }
                    $msg = "Please click on the link below to activate you account with Guestlist Social:

                    " . Router::url(array('action' => 'verify', 'id' => $id, 'h' => $hash), true);
                    $Email = new CakeEmail('default');
                    $Email->from(array('connect@guestlistsocial.com' => 'Guestlist Social'));
                    $Email->to($this->request->data['User']['email']);
                    $Email->subject('Confirm Registration for Guestlist Social');
                    $Email->send($msg);
	                $this->Session->setFlash(__('Please check your email to complete registration.'));
	                $this->redirect(array('controller' => 'users', 'action' => 'login'));
	            } else {
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }

	        }

            if (!empty($this->passedArgs['e']) && !empty($this->passedArgs['d'])) {
                $email = $this->passedArgs['e'] . '@' . $this->passedArgs['d'];
            } else {
                $email = false;
            };
            $this->set('email', $email);
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
            if (isset($this->request->data['Register'])) {
                $split = explode('@', $this->request->data['Register']['email']);
                $this->redirect(array('controller' => 'Users', 'action' => 'register', 'e' => $split[0], 'd' => $split[1]));
            }
        }

    	if ($this->request->is('post')) {
            if (isset($this->request->data['User'])) {
        	    if ($this->Auth->login()) {
        	        $this->User->id = $this->Session->read('Auth.User.id');
	                $this->User->saveField('session_id', $this->Session->id());

                    if ($this->Session->read('Auth.User.group_id') == 6) {//do not allow access if email not verified
                        $this->Session->destroy();
                        $this->Session->setFlash('You have not verified your e-mail address, please follow the link in the email sent to you when you registered.');
                        $this->redirect($this->Auth->logout());
                    }

                    $user = $this->User->find('all', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
                    $this->Session->write('Auth.User.Team', $user[0]['Team']);
           	        $this->redirect($this->Session->read('Auth.redirect'));
      	        } else {
           	        $this->Session->setFlash(__('Invalid username or password, try again'));
        	    }
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

                $Email = new CakeEmail('default');
                $Email->from(array('no-reply@guestlistsocial.com' => 'Guestlist Social'));
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

    public function manage() {
        $user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
        $firstname = $user['User']['first_name'];
        $lastname = $user['User']['last_name'];
        $this->set('firstname', $firstname);
        $this->set('lastname', $lastname);

        if (empty($this->request->data['User']['password'])) {
            unset($this->request->data['User']['password']);
        }

        if ($this->request->data) {
            $this->User->id = $this->Session->read('Auth.User.id');
            $this->User->save($this->request->data);
        }
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
    debug($group);
    $group->id = 1;
    $this->Acl->allow($group, 'controllers');
    //$group->id = 1;
    //$this->Acl->allow($group, 'controllers/comments/commentSave');
    //$this->Acl->allow($group, 'controllers/teams/removeFromTeam');
    //$group->id = 2;
    //$this->Acl->allow($group, 'controllers/comments/commentSave');
    //$group->id = 5;
    //$this->Acl->allow($group, 'controllers/comments/commentSave'); 
    //$this->Acl->allow($group, 'controllers/teams/removeFromTeam');
    //$group->id = 7;
    //$this->Acl->allow($group, 'controllers/comments/commentSave');
    // we add an exit to avoid an ugly "missing views" error message
    echo "all done";
    exit;
}
}