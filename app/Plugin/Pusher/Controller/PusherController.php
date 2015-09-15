<?php

class PusherController extends PusherAppController {
	
	public $components = array('Auth', 'RequestHandler', 'Pusher.Pusher');
	var $uses = array('TwitterPermission', 'Team');

	public function auth() {
		if($this->request->is('post') && isset($this->request->data['channel_name']) && isset($this->request->data['socket_id'])) {
			$authData = '';
			switch($this->Pusher->getChannelType($this->request->data['channel_name'])) {
				case 'private':
					$myTeamIDs = array();
					foreach ($this->Session->read('Auth.User.Team') as $key) {
						array_push($myTeamIDs, $key['id']);
					}
					$permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $myTeamIDs)));
					if (in_array($this->Session->read('access_token.account_id'), $permissions)) {
						$authData = $this->Pusher->privateAuth(
								$this->request->data['channel_name'],
								$this->request->data['socket_id']
						);
					} else {
						throw new ForbiddenException();
					}
					break;
				case 'presence':
					//todo
					break;
				default:
					throw new MethodNotAllowedException();
					break;
			}
			$this->set('auth', $authData);
		}
		else {
			throw new MethodNotAllowedException();
		}
	}

}

?>