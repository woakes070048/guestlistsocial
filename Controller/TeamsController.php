<?php 
class TeamsController extends AppController {
    var $uses = array('User', 'Team', 'TwitterAccount', 'TwitterPermission', 'TeamsUser', 'Ticket', 'Tweet');
    public $helpers =  array('Html' , 'Form');
    public $components = array('Tickets');
    
    public function beforeFilter() {
        parent::beforeFilter();
    }


	public function manage() {
		$data = $this->request->data;
		if (isset($data['name'])) {
			$data['hash'] = substr(md5(rand()), 0, 20);;
			$this->Team->save($data);
			$id = $this->Team->getLastInsertId();
			
			$teamHash = $this->Tickets->set($id);

			$this->addtoTeam($teamHash);
			$this->User->id = $this->Session->read('Auth.User.id');
			$this->User->saveField('group_id', 1);
			$this->Session->write('Auth.User.Group.id', 1);
			$this->Session->write('Auth.User.group_id', 1);

		$this->redirect('/twitter/admin');
		}
	}

	public function manageteam() {
		$conditions = array('team_id' => $this->Session->read('Auth.User.Team.0.id'));


		if ($this->Session->read('Auth.User.Team')) {
			$permissions = array();
			foreach ($this->Session->read('Auth.User.Team') as $key) {
				if ($key['TeamsUser']['group_id'] == 1) {
            	$permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            	$permissions = array_merge($permissionsx, $permissions);
            	}
        	}
            $ddconditions = array('account_id' => $permissions);
        } else {
            $ddconditions = array('user_id' => $this->Session->read('Auth.User.id'));
        }

        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $dropdownaccounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'order' => array('screen_name' => 'ASC')));
        } else {
            $dropdownaccounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => $ddconditions, 'order' => array('screen_name' => 'ASC')));
        }
		$this->set('dropdownaccounts', $dropdownaccounts);
		$accounts = $this->TwitterAccount->find('all', array('fields' => array('screen_name', 'account_id'), 'conditions' => $ddconditions, 'order' => array('screen_name' => 'ASC')));
		$this->set('accounts', $accounts);


		foreach ($this->Session->read('Auth.User.Team') as $key) {
			$dropdownusers = $this->User->Team->find('all', array('conditions' => array('Team.id' => $key['id'])));
			foreach ($dropdownusers as $key1) {
				foreach ($key1['User'] as $key2) {
					$dropdownusers1[$key2['id']] = $key2['first_name'];
				}
			}

			$dropdownteams = $this->Team->find('all', array('conditions' => array('id' => $key['id'])));
			$dropdownteams1[$key['id']] = $dropdownteams[0]['Team']['name'];
		}
		$this->set('dropdownusers', $dropdownusers1);
		$this->set('dropdownteams', $dropdownteams1);


		if (isset($this->request->data['filterAccount'])) {//If filtering by account
			$twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => 'account_id', 'conditions' => array('screen_name' => $this->request->data['filterAccount']['account'])));
			$users1 = $dropdownusers1;
			$users1 = $dropdownteams1;
			foreach ($users1 as $key => $value) {
				$permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key)));
				$users[$value] = array('team_id' => $key , 'name' => $value, 'permissions' => $permissions);
			}
			$this->set('twitter_account_id', (int)$twitter_account_id['TwitterAccount']['account_id']);
			$this->set('currentAccount', $this->request->data['filterAccount']['account']);
			$this->set('accountTable', true);


		} elseif (isset($this->request->data['filterTeam'])) {//If filtering by team
			$teamx = $this->Team->find('all', array('conditions' => array('id' => $this->request->data['filterTeam']['team'])));
				$permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $this->request->data['filterTeam']['team'])));
				$users = array('team_id' => $teamx[0]['Team']['id'], 'name' => $teamx[0]['Team']['name'], 'permissions' => $permissions);
			$currentTeam = $this->Team->find('first', array('fields' => array('name'), 'conditions' => array('id' => $this->request->data['filterTeam']['team'])));
			$teamMembers = $this->User->Team->find('all', array('conditions' => array('Team.id' => $this->request->data['filterTeam']['team'])));
        	$this->set('teamMembers', $teamMembers[0]['User']);
			$this->set('currentTeam', $currentTeam['Team']['name']);
			$this->set('currentTeamId', $this->request->data['filterTeam']['team']);
			$this->Session->write('Auth.User.currentTeamId', $this->request->data['filterTeam']['team']);
			$this->set('teamTable', true);
		} else {
			$users = '';
		}

		$this->set('users', $users);


		//right panel
		$allusers = array();
		foreach ($this->Session->read('Auth.User.Team') as $key) {
			$id = $key['id'];
			$users = $this->TeamsUser->find('list', array('fields' => 'user_id', 'conditions' => array('team_id' => $id)));
			$allusers = array_merge($allusers, $users);
			$allusers = array_unique($allusers);
		}

		$base = strtotime(date('Y-m-d',time()) . '-01 00:00:01');
		$counts = array();
		foreach ($allusers as $key => $value) {
			$user = $this->User->find('first', array('conditions' => array('User.id' => $value)));
			$counts[$value][6] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('-6 day', $base)), date("Y-m-d H:i:s", strtotime('-5 day', $base))))));
			$counts[$value][5] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('-5 day', $base)), date("Y-m-d H:i:s", strtotime('-4 day', $base))))));
			$counts[$value][4] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('-4 day', $base)), date("Y-m-d H:i:s", strtotime('-3 day', $base))))));
			$counts[$value][3] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('-3 day', $base)), date("Y-m-d H:i:s", strtotime('-2 day', $base))))));
			$counts[$value][2] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('-2 day', $base)), date("Y-m-d H:i:s", strtotime('-1 day', $base))))));
			$counts[$value][1] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('-1 day', $base)), date("Y-m-d H:i:s", strtotime('0 day', $base))))));
			$counts[$value][0] = $this->Tweet->find('count', array('conditions' => array('user_id' => $value, 'created between ? and ?' => array(date("Y-m-d H:i:s", strtotime('0 day', $base)), date("Y-m-d H:i:s", time())))));		
			$counts[$value]['sum'] = array_sum($counts[$value]);
			$counts[$value]['name'] = $user['User']['first_name'];
		}
		function cmp_by_sum($a, $b) {
		  return $b['sum'] - $a['sum'];
		}

		usort($counts, 'cmp_by_sum');
		$this->set('counts', $counts);
        //$teamMembers = debug($this->User->find('all', array('fields' => array('first_name', 'group_id', 'id'))));
	}

	public function permissionSave() {
		$data = $this->request->data;
		//$dbComparisons = $this->TwitterPermission->find('all', array('conditions' => array('team_id' => $this->Session->read('Auth.User.Team.id'))));
		$i = 0;
		foreach ($data['Teams'] as $key) {
			foreach ($key['permissions'] as $key1 => $value1) {
				$i++;
				if ($value1 !== '0') {
					//$dbComparisons = $this->TwitterPermission->find('count', array('consitions' => array('team_id' => $key['team_id'], 'user_id' => $value1, 'twitter_account_id' => $key1)));
					//check if permission exists
					if ($this->TwitterPermission->hasAny(array('team_id' => $value1, 'twitter_account_id' => $key1))) {
					} else {
						$this->TwitterPermission->create();
						$this->TwitterPermission->saveField('user_id', $this->Session->read('Auth.User.id'));
						$this->TwitterPermission->saveField('twitter_account_id', $key1);
						$this->TwitterPermission->saveField('team_id', $value1);
						//check if already exists in db then go back to admin.ctp...
					}
				} elseif ($value1 == '0') {
					//deleting
					$idx = $this->TwitterPermission->find('list', array('fields' => array('id'), 'conditions' => array('team_id' => $key['team_id'], 'twitter_account_id' => $key1)));
					if ($idx) {
					$this->TwitterPermission->delete($idx);
					}
				}
			}
		}
		echo $i;
		$this->Session->setFlash('Changes successfully saved');
		$this->redirect('/teams/manageteam');
	}

	public function addtoTeam($teamHash, $group = null, $permissions = null) {
		$team = $this->Team->find('first', array('fields' => array('id', 'name', 'hash'), 'conditions' => array('id' => $this->Tickets->get($teamHash))));

		if ($this->referer() === 'http://social.guestlist.net/teams/manageteam') {
			$group = 1;
		}

		$save = array(
					'TeamsUser' => array (
						'user_id' => $this->Session->read('Auth.User.id'),
						'team_id' => $this->Tickets->get($teamHash),
						'group_id' => $this->Tickets->get($group)
					)
				);
		$this->TeamsUser->save($save);
		$this->Tickets->del($teamHash);
		$this->Tickets->del($group);

		$this->refreshGroup($this->Session->read('Auth.User.id'));

		$user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('Auth.User.id'))));
		$this->Session->write('Auth.User.Team', $user['Team']);
		$this->Session->setFlash('You have been added to team ' . $team['Team']['name']);
		$this->redirect('/');
	}

	public function removeFromTeam($user_id, $team_id) {
		if ($this->TeamsUser->hasAny(array('user_id' => $this->Session->read('Auth.User.id'), 'team_id' => $team_id))) {
			$this->User->id = $user_id;

			$id = $this->TeamsUser->find('first', array('fields' => 'id', 'conditions' => array('user_id' => $user_id, 'team_id' => $team_id)));
			$this->TeamsUser->delete($id['TeamsUser']['id']);
		}

		$this->refreshGroup($user_id);

		$this->redirect('/teams/manageteam');
	}

	public function makeadmin($user_id, $team_id) {
		if ($this->TeamsUser->hasAny(array('user_id' => $this->Session->read('Auth.User.id'), 'team_id' => $team_id))) {
			$teamsuser = $this->TeamsUser->find('first', array('conditions' => array('user_id' => $user_id, 'team_id' => $team_id)));
			$this->TeamsUser->id = $teamsuser['TeamsUser']['id'];
			$this->TeamsUser->savefield('group_id', 1);
		}

		$this->refreshGroup($user_id);

		$this->redirect('/teams/manageteam');
	}

	public function makeproofer($user_id, $team_id) {
		if ($this->TeamsUser->hasAny(array('user_id' => $this->Session->read('Auth.User.id'), 'team_id' => $team_id))) {
			$teamsuser = $this->TeamsUser->find('first', array('conditions' => array('user_id' => $user_id, 'team_id' => $team_id)));
			$this->TeamsUser->id = $teamsuser['TeamsUser']['id'];
			$this->TeamsUser->savefield('group_id', 7);
		}

		$this->refreshGroup($user_id);
		
		$this->redirect('/teams/manageteam');
	}

	public function removeadmin($user_id, $team_id) {
		if ($this->TeamsUser->hasAny(array('user_id' => $this->Session->read('Auth.User.id'), 'team_id' => $team_id))) {
			$teamsuser = $this->TeamsUser->find('first', array('conditions' => array('user_id' => $user_id, 'team_id' => $team_id)));
			$this->TeamsUser->id = $teamsuser['TeamsUser']['id'];
			$this->TeamsUser->savefield('group_id', 2);
		}

		$this->refreshGroup($user_id);

		$this->redirect('/teams/manageteam');
	}

	public function invite() {
		foreach ($this->Session->read('Auth.User.Team') as $key) {
			$dropdownteams[$key['id']] = $key['name'];
		}
		$this->set('dropdownteams', $dropdownteams);
		if ($this->request->data) {
			$data = $this->request->data;
			$user = $data['invite']['email'];
			$team = $this->Team->find('first', array('conditions' => array('id' => $data['invite']['team'])));

			$count = $this->User->find('count', array('conditions' => array('email' => $user)));
			$teamname = $team['Team']['name'];
			$first_name = $this->Session->read('Auth.User.first_name');
			$last_name = $this->Session->read('Auth.User.last_name');
			$hash = $this->Tickets->set($data['invite']['team']);
			$grouphash = $this->Tickets->set($data['invite']['group']);

			$Email = new CakeEmail();
            $Email->from(array('registration@social.guestlist.net' => 'Guestlist Social'));
            $Email->to($user);

			if ($count == 0) {
            	$Email->subject('You have been invited to join Guestlist Social');
				$msg = "You have been invited to join Guestlist Social by $first_name $last_name. $first_name has also invited you to join their team! Click the link below to register and be automatically added to their team.

				" . Router::url(array('controller' => 'users', 'action' => 'register', 'h' => $hash, 'g' => $grouphash), true);
            	$Email->send($msg);
            	$this->Session->setFlash(__('Invite Sent.'));
			} elseif ($count == 1) {
            	$Email->subject('You have been invited to join a team at Guestlist Social');
				$msg = "You have been invited to join the team $teamname by $first_name $last_name. Click the link below to join their team!

				" . Router::url(array('action' => 'addtoTeam/' . $hash . '/' . $grouphash), true);
				$Email->send($msg);
            	$this->Session->setFlash(__('Invite Sent.'));
			}
		}
	}
}