<?php 
class TeamsController extends AppController {
    var $uses = array('User', 'Team', 'TwitterAccount', 'TwitterPermission', 'TeamsUser', 'Ticket', 'Tweet', 'EditorialCalendar');
    public $helpers =  array('Html' , 'Form');
    public $components = array('Tickets');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('test', 'edit', 'users', 'permissionSave1', 'editrefresh');
    }


	public function manage() {
		$data = $this->request->data;
		if (isset($data['name'])) {
			$data['hash'] = substr(md5(rand()), 0, 20);
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
		$base = strtotime(date('Y-m-d',time()) . '-01 00:00:01');
		$myTeamIDs = array();
		$ddTeams = array();
		foreach ($this->Session->read('Auth.User.Team') as $key) {
			array_push($myTeamIDs, $key['id']);
			$ddTeams[$key['id']] = $key['name'];
		}
		$this->set('ddTeams', $ddTeams);
		if ($this->request->data) {
			//monthselector
			$months = $this->request->data['Team']['Select Month'];
			$this->set('months', $months);


			$team_id = $this->request->data['Team']['id'];
			$twitter_accounts = $this->TwitterPermission->find('all', array('conditions' => array('team_id' => $team_id)));
			$query_twitter_accounts = array();
			$screen_names = array();
			foreach ($twitter_accounts as $key) {
				array_push($query_twitter_accounts, $key['TwitterPermission']['twitter_account_id']);
			}
			$screen_names = $this->TwitterAccount->find('list', array('fields' => array('account_id', 'screen_name'), 'conditions' => array('account_id' => $query_twitter_accounts)));
				
			$query_twitter_accounts1 = join(',', $query_twitter_accounts);

			//$tweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => $query_twitter_accounts)));
			$firstdate = strtotime(date('M Y') . ' + ' . ($months) . 'months');//need to be able to select months
			$seconddate = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
			/*$totalCount = $this->Tweet->query("SELECT COUNT(user_id), account_id, verified
											FROM tweets
											WHERE timestamp BETWEEN  '$firstdate' AND '$seconddate' AND
											account_id IN ($query_twitter_accounts1) AND calendar_id <> ''
											GROUP BY account_id, verified");*/

			/*$tweetCount = $this->Tweet->query("SELECT COUNT(user_id), user_id
											FROM tweets
											WHERE timestamp BETWEEN  '$firstdate' AND '$seconddate' AND
											account_id IN ($query_twitter_accounts1)
											GROUP BY user_id");*/
			$userIDs = array();
			foreach ($tweetCount as $key) {
				$userIDs[] = $key['tweets']['user_id'];
			}

			$userNames = $this->User->find('all', array('fields' => array('last_name', 'first_name', 'profile_pic', 'id'), 'conditions' => array('User.id' => $userIDs), 'recursive' => -1));
			$userNames = Hash::combine($userNames, '{n}.User.id', '{n}');
			
			$tweetCount1 = array();
			$barChartLabels = array();
			$barChartData = array();
			foreach ($tweetCount as $key) {
				//$tweetCount1[$key['tweets']['account_id']][$key['tweets']['user_id']]['name'] = $userNames[$key['tweets']['user_id']]['User']['first_name'] . $userNames[$key['tweets']['user_id']]['User']['last_name'];
				//$tweetCount1[$key['tweets']['account_id']][$key['tweets']['user_id']][$key['tweets']['verified']] = $key[0]['COUNT(user_id)'];
				//$tweetCount1[$key['tweets']['account_id']][$key['tweets']['user_id']]['profile_pic'] = $userNames[$key['tweets']['user_id']]['User']['profile_pic'];

				$tweetCount1[$key['tweets']['user_id']]['name'] = $userNames[$key['tweets']['user_id']]['User']['first_name'] . ' ' . $userNames[$key['tweets']['user_id']]['User']['last_name'];
				$tweetCount1[$key['tweets']['user_id']]['profile_pic'] = $userNames[$key['tweets']['user_id']]['User']['profile_pic'];
				$tweetCount1[$key['tweets']['user_id']]['count'] = $key[0]['COUNT(user_id)'];

				array_push($barChartLabels, $userNames[$key['tweets']['user_id']]['User']['first_name'] . $userNames[$key['tweets']['user_id']]['User']['last_name']);
				array_push($barChartData, $key[0]['COUNT(user_id)']);
			}
			$this->set('barChartLabels', json_encode($barChartLabels));
			$this->set('barChartData', json_encode($barChartData));
			
			$totalCount1 = array();
			foreach ($totalCount as $key) {
				$totalCount1[$key['tweets']['account_id']][$key['tweets']['verified']] = $key[0]['COUNT(user_id)'];
			}
			
			$calendarCount = $this->EditorialCalendar->query("SELECT COUNT(id), twitter_account_id, id
															FROM editorial_calendars
															WHERE twitter_account_id IN ($query_twitter_accounts1)
															GROUP BY twitter_account_id");
			foreach ($calendarCount as $key1) {
				if (!empty($totalCount1[$key1['editorial_calendars']['twitter_account_id']])) {
					$totalCount1[$key1['editorial_calendars']['twitter_account_id']]['calendarCount'] = $key1[0]['COUNT(id)'] / 7;
					$totalCount1[$key1['editorial_calendars']['twitter_account_id']]['screen_name'] = $screen_names[$key1['editorial_calendars']['twitter_account_id']];
				}
			}
			//debug($calendarCount);

			//debug($totalCount1);
			//debug($tweetCount1);
			//debug($userNames);
			$this->set('query_twitter_accounts', $query_twitter_accounts);
			$this->set('totalCount1', $totalCount1);
			$this->set('tweetCount1', $tweetCount1);
			$this->set('userNames', $userNames);
			$this->set('screen_names', $screen_names);

			$calendarIDs = $this->EditorialCalendar->find('list', array('fields' => 'id', 'conditions' => array('EditorialCalendar.twitter_account_id' => $query_twitter_accounts)));
			$tableTweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => $query_twitter_accounts, 'timestamp >=' => $firstdate, 'timestamp <=' => $seconddate, 'calendar_id' => $calendarIDs), 'recursive' => 0, 'order' => 'Tweet.modified DESC'));
			
			$tableTweets1 = array();
			foreach ($tableTweets as $key) {
				if (empty($tableTweets1[$key['Tweet']['account_id']][date('jS', $key['Tweet']['timestamp'])][$key['Tweet']['verified']])) {
					$tableTweets1[$key['Tweet']['account_id']][date('jS', $key['Tweet']['timestamp'])][$key['Tweet']['verified']] = 0;
				}
				$tableTweets1[$key['Tweet']['account_id']][date('jS', $key['Tweet']['timestamp'])][$key['Tweet']['verified']] += 1;
			}
			$this->set('tableTweets1', $tableTweets1);

			$monthdate = strtotime(date('M Y'));
			$weekdate = strtotime('Monday this week');
			$daydate = strtotime('Today');
			$monthCount = $this->Tweet->find('count', array('conditions' => array('Tweet.account_id' => $query_twitter_accounts, 'Tweet.created >' => date('Y-m-d', $monthdate))));
			$weekCount = $this->Tweet->find('count', array('conditions' => array('Tweet.account_id' => $query_twitter_accounts, 'Tweet.created >' => date('Y-m-d', $weekdate))));
			$dayCount = $this->Tweet->find('count', array('conditions' => array('Tweet.account_id' => $query_twitter_accounts, 'Tweet.created >' => date('Y-m-d', $daydate))));
			$this->set('monthCount', $monthCount);
			$this->set('weekCount', $weekCount);
			$this->set('dayCount', $dayCount);
		} else {
			$months = 0;
			$this->set('months', $months);
		}
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

		if ($this->referer() === 'http://social.guestlist.net/teams/manage') {
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
		if ($this->TeamsUser->hasAny(array('user_id' => $this->Session->read('Auth.User.id'), 'team_id' => $team_id, 'group_id' => 1))) {
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

			$Email = new CakeEmail('default');
            $Email->from(array('no-reply@guestlistsocial.com' => 'Guestlist Social'));
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

	public function twitter() {
		$this->TwitterPermission->find('list');
	}

	public function users() {
		debug(json_encode((array('keywords' => array(array('111', '222', '333'), array('444', '555', '666'))))));
	}

	public function edit() {
		$conditions = array('team_id' => $this->Session->read('Auth.User.Team.0.id'));

		if ($this->Session->read('Auth.User.Team')) {
			$permissions = array();
			$teamIDs = array();
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
        $this->set('permissions', $permissions);

        
        $dropdownaccounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => $ddconditions, 'order' => array('screen_name' => 'ASC')));
        
		$this->set('dropdownaccounts', $dropdownaccounts);

		$dropdownteams1 = array();
		foreach ($this->Session->read('Auth.User.Team') as $key) {
			if ($key['TeamsUser']['group_id'] == 1) {
				$dropdownteams = $this->Team->find('all', array('conditions' => array('id' => $key['id'])));
				$dropdownteams1[$key['id']] = $dropdownteams[0]['Team']['name'];
			}
		}
		$this->set('dropdownteams', $dropdownteams1);

		if (!empty($this->request->data['filterTeam']) && $this->request->data['filterTeam']['team'] != 'empty') {
			$accounts = $this->TwitterAccount->find('all', array('fields' => array('screen_name', 'account_id'), 'conditions' => $ddconditions, 'order' => array('screen_name' => 'ASC')));
			$this->set('accounts', $accounts);
			$this->set('currentTeam', $this->request->data['filterTeam']['team']);
			$usersPermissions = $this->TeamsUser->find('all', array('fields' => array('user_id', 'group_id', 'id'), 'conditions' => array('team_id' => $this->request->data['filterTeam']['team'])));
			$usersPermissions = Hash::combine($usersPermissions, '{n}.TeamsUser.user_id', '{n}');
			foreach ($usersPermissions as $key1 => $value1) {
				$usersPermissions1[] = $value1['TeamsUser']['user_id'];
			}
			$accountPermissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $this->request->data['filterTeam']['team'])));
			
			$users = $this->User->find('all', array('conditions' => array('User.id' => $usersPermissions1)));
			$this->Session->write('Auth.User.currentTeamId');
			$this->set('users', $users);
			$this->set('usersPermissions', $usersPermissions);
			$this->set('accountPermissions', $accountPermissions);
		}

		if (!empty($this->request->data['filterAccount']) && $this->request->data['filterAccount']['account'] != 'empty') {
			$twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => 'account_id', 'conditions' => array('screen_name' => $this->request->data['filterAccount']['account'])));
			$teams = $this->TwitterPermission->find('list', array('fields' => array('id', 'team_id'), 'conditions' => array('twitter_account_id' => $twitter_account_id['TwitterAccount']['account_id'])));
			$allTeams = $this->TeamsUser->find('list', array('fields' => array('id', 'team_id'), 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'), 'group_id' => 1)));
			$this->set('teams', $teams);
			$this->set('allTeams', $allTeams);
			$this->set('currentAccount', $twitter_account_id['TwitterAccount']['account_id']);
			$teamsName = $this->Team->find('all', array('fields' => array('id', 'name'), 'conditions' => array('Team.id' => $allTeams), 'recursive' => -1));
			$teamsName = Hash::combine($teamsName, '{n}.Team.id', '{n}');
			$this->set('teamsName', $teamsName);
		}

	}

	public function permissionSave1() {
		if (!empty($this->request->data['Accounts'])) {
			foreach ($this->request->data['Accounts'] as $key => $value) {
				if ($value['permissions'][$key] !== '') {//if changed
					if ($this->TwitterPermission->hasAny(array('team_id' => $value['team_id'], 'twitter_account_id' => $key))) {//if exists in db
						if ($value['permissions'][$key] == 0) {//if 0
							$permission = $this->TwitterPermission->find('first', array('fields' => 'id', 'conditions' => array('team_id' => $value['team_id'], 'twitter_account_id' => $key)));
							$this->TwitterPermission->delete($permission['TwitterPermission']['id']);
						}
					} else {
						if ($value['permissions'][$key] == 1) {//if 1
							$toSave['TwitterPermission']['twitter_account_id'] = $key;
							$toSave['TwitterPermission']['team_id'] = $value['team_id'];
							$this->TwitterPermission->save($toSave);
						}
					}
				} else {

				}
			}		
		}

		if (!empty($this->request->data['Users'])) {
			foreach ($this->request->data['Users'] as $key => $value) {
				$array = array('id' => $key, 'team_id' => $value['team_id'], 'user_id' => key($value['permissions']), 'group_id' => $value['permissions'][key($value['permissions'])]);
				$toSave[] = $array;
			}
			$this->TeamsUser->saveAll($toSave);
			$this->Session->setFlash('Saved successfully');
		}

		if (!empty($this->request->data['Teams'])) {
			foreach ($this->request->data['Teams'] as $key => $value) {
				if ($this->TwitterPermission->hasAny(array('team_id' => $key, 'twitter_account_id' => key($value['permissions'])))) {
					if ($value['permissions'][$value['account_id']] == 0) {//if 0
						$permission = $this->TwitterPermission->find('first', array('fields' => 'id', 'conditions' => array('twitter_account_id' => $value['account_id'], 'team_id' => $key)));
						$this->TwitterPermission->delete($permission['TwitterPermission']['id']);
					}
				} else {
					if ($value['permissions'][$value['account_id']] == 1) {//if 1
						$toSave['TwitterPermission']['twitter_account_id'] = $value['account_id'];
						$toSave['TwitterPermission']['team_id'] = $key;
						$this->TwitterPermission->save($toSave);
					}
				}
			}

		}
		$this->redirect(Controller::referer());
	}

	public function editrefresh($team_id, $account_id) {
		$conditions = array('team_id' => $this->Session->read('Auth.User.Team.0.id'));

		if ($this->Session->read('Auth.User.Team')) {
			$permissions = array();
			$teamIDs = array();
			foreach ($this->Session->read('Auth.User.Team') as $key) {
				if ($key['TeamsUser']['group_id'] == 1) {
            	$permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            	$permissions = array_merge($permissionsx, $permissions);
            	$teamIDs[] = $key['id'];
            	}
        	}
            $ddconditions = array('account_id' => $permissions);
        } else {
            $ddconditions = array('user_id' => $this->Session->read('Auth.User.id'));
        }
        $this->set('permissions', $permissions);

        
        $dropdownaccounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => $ddconditions, 'order' => array('screen_name' => 'ASC')));
        
		$this->set('dropdownaccounts', $dropdownaccounts);

		$dropdownteams1 = array();
		foreach ($this->Session->read('Auth.User.Team') as $key) {
			if ($key['TeamsUser']['group_id'] == 1) {
				$dropdownteams = $this->Team->find('all', array('conditions' => array('id' => $key['id'])));
				$dropdownteams1[$key['id']] = $dropdownteams[0]['Team']['name'];
			}
		}
		$this->set('dropdownteams', $dropdownteams1);

		if ($team_id != 'null' && $account_id == 'null') {
			if ($this->TeamsUser->hasAny(array('user_id' => $this->Session->read('Auth.User.id'), 'team_id' => $team_id, 'group_id' => 1))) {
				$accounts = $this->TwitterAccount->find('all', array('fields' => array('screen_name', 'account_id'), 'conditions' => $ddconditions, 'order' => array('screen_name' => 'ASC')));
				$this->set('accounts', $accounts);
				$this->set('currentTeam', $team_id);
				$usersPermissions = $this->TeamsUser->find('all', array('fields' => array('user_id', 'group_id', 'id'), 'conditions' => array('team_id' => $team_id)));
				$usersPermissions = Hash::combine($usersPermissions, '{n}.TeamsUser.user_id', '{n}');
				foreach ($usersPermissions as $key1 => $value1) {
					$usersPermissions1[] = $value1['TeamsUser']['user_id'];
				}
				$accountPermissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $team_id)));
				
				$users = $this->User->find('all', array('conditions' => array('User.id' => $usersPermissions1)));
				$this->set('users', $users);
				$this->set('usersPermissions', $usersPermissions);
				$this->set('accountPermissions', $accountPermissions);
			} else {
				$this->Session->setFlash('You do not have permission to edit this team.');
			}
		}

		if ($team_id == 'null' && $account_id != 'null') {
			if ($this->TwitterPermission->hasAny(array('twitter_account_id' => $account_id, 'team_id' => $teamIDs))) {
				//$twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => 'account_id', 'conditions' => array('screen_name' => $account_id)));
				$teams = $this->TwitterPermission->find('list', array('fields' => array('id', 'team_id'), 'conditions' => array('twitter_account_id' => $account_id)));
				$allTeams = $this->TeamsUser->find('list', array('fields' => array('id', 'team_id'), 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'), 'group_id' => 1)));
				$this->set('teams', $teams);
				$this->set('allTeams', $allTeams);
				$this->set('currentAccount', $account_id);
				$teamsName = $this->Team->find('all', array('fields' => array('id', 'name'), 'conditions' => array('Team.id' => $allTeams), 'recursive' => -1));
				$teamsName = Hash::combine($teamsName, '{n}.Team.id', '{n}');
				$this->set('teamsName', $teamsName);
			} else {
				$this->Session->setFlash('You do not have permission to edit this account.');
			}
		}
		$this->layout = '';
	}
}