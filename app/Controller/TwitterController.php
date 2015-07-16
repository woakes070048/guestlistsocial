<?php
App::import('Vendor', 'OAuth/OAuthClient');

class TwitterController extends AppController {
    public $components = array('Session', 'Auth', 'Paginator', 'Tickets');
    public $helpers =  array('Html' , 'Form');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar', 'Ticket', 'TeamsUser', 'Team', 'Notification', 'Editor');

    public function index() {
        if (isset($this->request->data['currentmonth'])) {
            $this->Session->write('Auth.User.monthSelector', $this->request->data['currentmonth']['Select Month']);
        } elseif ($this->Session->read('Auth.User.monthSelector') == false) {
            $this->Session->write('Auth.User.monthSelector', 0);
        }

        if (!empty($this->request->query['s'])) {
            $m = date('F', strtotime('+ ' . $this->request->query['m'] . ' months'));
            $d = date('jS', strtotime($this->request->query['s'] . ' ' . $m));
            $this->set('scroll', $d);
            $this->Session->write('Auth.User.monthSelector', $this->request->query['m']);
            $acc = $this->TwitterAccount->find('first', array('conditions' => array('TwitterAccount.account_id' => $this->request->query['accid'])));
            $this->Session->write('access_token.account_id', $this->request->query['accid']);
            $this->Session->write('access_token.screen_name', $acc['TwitterAccount']['screen_name']);
        } else {
            $this->set('scroll', 0);
        }

        $permissions = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            $permissions = array_merge($permissionsx, $permissions);
            $myteam[$key['id']] = $key['name'];
        }
        $conditions = array('TwitterAccount.account_id' => $permissions);
        $this->set('myteam', $myteam);

        $dropdownaccounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => $conditions, 'order' => array('screen_name' => 'ASC')));
        $dropdownaccounts = array(0 => 'All Accounts') + $dropdownaccounts;
        $this->set('dropdownaccounts', $dropdownaccounts);

        //Setting Dropdown Users
        //Getting array of all users across all teams that user has access to
        $new = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $var = $this->TeamsUser->find('list', array('fields' => 'user_id', 'conditions' => array('team_id' => $key['id'])));
            $new = array_merge($new, $var);
        }

        //Getting array for use in view
        $dropdownusers = array(0 => 'All Users');
        foreach ($new as $key => $value) {
            $username = $this->User->find('first', array('fields' => 'first_name', 'conditions' => array('User.id' => $value)));
            $dropdownusers[$value] = $username['User']['first_name'];
        }
        //$dropdownusers = $this->User->find('list', array('fields' => array('id', 'first_name'), 'conditions' => $conditions));
        //$dropdownusers = array(0 => 'All Users') + $dropdownusers;
        $this->set('dropdownusers', $dropdownusers);

        /*$v = 0;
        $p = 0;
        $t = time();
        $timestamp = 'timestamp >';
        $order = 'asc';
        $this->set('params', '');
        if (isset($this->request->query['h'])) {
            if ($this->request->query['h'] == 'queued') {
                $v = 1;
                $p = 0;
                $this->set('params', 'h:queued');
            } elseif ($this->request->query['h'] == 'published') {
                $v = 1;
                $p = 1;
                $t = -1;
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->request->query['h'] == 'improving') {
                $v = 2;
                $p = 0;
                $this->set('params', 'h:improving');
            } elseif ($this->request->query['h'] == 'notpublished') {
                $v = array(0, 2);
                $p = 0;
                $timestamp = 'timestamp <';
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->request->query['h'] == 'daybyday') {
                $this->set('params', 'h:daybyday');
            }
        }

        if (isset($this->request->data['filterUser']['user']) && $this->request->data['filterUser']['user'] == 0) {
            $c = array('verified' => $v, 'published' => $p, $timestamp => $t);
            $this->Session->delete('filterAccount');
            $this->Session->delete('filterUser');
        } elseif (isset($this->request->data['filterAccount']['account']) && $this->request->data['filterAccount']['account'] == 'All Accounts') {
            $c = array('verified' => $v, 'published' => $p, $timestamp => $t);
            $this->Session->delete('filterAccount');
            $this->Session->delete('filterUser');
        } else {

            if (isset($this->request->data['filterUser']['user']) && $this->request->data['filterUser']['user'] != 'empty') {//If filltering by user
                $written_by = $this->User->find('first', array('fields' => 'first_name', 'conditions' => array('User.id' => $this->request->data['filterUser']['user'])));
                $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'user_id' => $this->request->data['filterUser']['user']);
                $this->Session->write('filterUser', $this->request->data['filterUser']['user']);
                $this->Session->delete('filterAccount');
            } elseif (isset($this->request->data['filterAccount']['account']) && $this->request->data['filterAccount']['account'] != 'empty') {//If filtering by account
                $twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => array('account_id', 'screen_name'), 'conditions' => array('screen_name' => $this->request->data['filterAccount']['account'])));
                $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'account_id' => $twitter_account_id['TwitterAccount']['account_id']);
                $this->Session->write('access_token.account_id', $twitter_account_id['TwitterAccount']['account_id']);
                $this->Session->write('access_token.screen_name', $twitter_account_id['TwitterAccount']['screen_name']);
                $this->Session->write('filterAccount', $this->request->data['filterAccount']['account']);
                $this->Session->delete('filterUser');
            } elseif ($this->Session->read('filterUser')) {
                $written_by = $this->User->find('first', array('fields' => 'first_name', 'conditions' => array('User.id' => $this->Session->read('filterUser'))));
                $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'user_id' => $this->Session->read('filterUser'));
            } elseif ($this->Session->read('filterAccount')) {
                $twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => 'account_id', 'conditions' => array('screen_name' => $this->Session->read('filterAccount'))));
                $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'account_id' => $twitter_account_id['TwitterAccount']['account_id']);
            } else {
                $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'account_id' => $permissions);
            }

        }*/
        
        $v = array(0, 1, 2);
        $p = 0;
        $t = time();
        $timestamp = 'timestamp >';
        $order = 'asc';
        $twitter_account_id['TwitterAccount']['account_id'] = $permissions;
        $this->set('status', $this->Session->read('filter.status'));
        $this->set('user', $this->Session->read('filter.user'));
        $this->set('account', $this->Session->read('filter.account'));
        $this->set('team', $this->Session->read('filter.team'));
        if (!empty($this->request->query['h'])) {
            if ($this->request->query['h'] == 'daybyday') {
                $this->set('params', 'h:daybyday');
            }
        } else {
            $this->set('params', '');
        }

        $filter = $this->Session->read('filter');

        if (!empty($this->request->data['filter'])) {
            $filter1 = $this->request->data['filter'];
            if ($filter1['status']) {
                $filter['status'] = $filter1['status'];
            }
            if ($filter1['user']) {
                $filter['user'] = $filter1['user'];
            }
            if ($filter1['account']) {
                $filter['account'] = $filter1['account'];
            }
            if ($filter1['team']) {
                $filter['team'] = $filter1['team'];
            }
        }

        if (!empty($filter['status'])) {
            if ($filter['status'] == 'queued') {
                $v = 1;
                $this->set('status', 'queued');
                $this->Session->write('filter.status', 'queued');
            } elseif ($filter['status'] == 'awaitingproof') {
                $v = 0;
                $this->set('status', 'awaitingproof');
                $this->Session->write('filter.status', 'awaitingproof');
            } elseif ($filter['status'] == 'improving') {
                $v = 2;
                $this->set('status', 'improving');
                $this->Session->write('filter.status', 'improving');
            } elseif ($filter['status'] == 'published') {
                $p = 1;
                $t = -1;
                $order = 'desc';
                $this->set('status', 'published');
                $this->Session->write('filter.status', 'published');
            } elseif ($filter['status'] == 'notpublished') {
                $v = array(0, 2);
                $p = 0;
                $timestamp = 'timestamp <';
                $order = 'desc';
                $this->set('status', 'notpublished');
                $this->Session->write('filter.status', 'notpublished');
            } elseif ($filter['status'] == 'All Statuses') {
                $this->set('status', 'All Statuses');
                $this->Session->write('filter.status', 'All Statuses');
            }
        }

        if (!empty($filter['user'])) {
            $user_id = $filter['user'];
            $this->set('user', $user_id);
            $this->Session->write('filter.user', $user_id);
        }

        if (!empty($filter['account'])) {
            if (empty($this->request->query['s'])) {
                $twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => array('account_id', 'screen_name'), 'conditions' => array('screen_name' => $filter['account'])));
                if ($filter['account'] == 'All Accounts') {
                    $twitter_account_id['TwitterAccount']['account_id'] = $permissions;
                } else {
                    $this->Session->write('access_token.account_id', $twitter_account_id['TwitterAccount']['account_id']);
                    $this->Session->write('access_token.screen_name', $twitter_account_id['TwitterAccount']['screen_name']);
                }
                $this->set('account', $filter['account']);
                $this->Session->write('filter.account', $filter['account']);
            }
        }

        if (!empty($filter['team'])) {
            $twitter_account_id['TwitterAccount']['account_id'] = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $filter['team'])));
            $this->set('team', $filter['team']);
            $this->Session->write('filter.team', $filter['team']);
        }

        if (!empty($user_id)) {
            $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'Tweet.account_id' => $twitter_account_id['TwitterAccount']['account_id'], 'Tweet.user_id' => $user_id);
        } else {
            $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'Tweet.account_id' => $twitter_account_id['TwitterAccount']['account_id']);
        }
        
        $this->Paginator->settings = array(
        'conditions' => $c,
        'limit' => 10,
        'order' => array('timestamp' => $order,
        'paramType' => 'queryString'),
        'recursive' => 2
        );

        $countConditions0 = array('verified' => 0, 'published' => 0, 'timestamp >' => time(), 'Tweet.account_id' => $permissions);
        $countConditions1 = array('verified' => 1, 'published' => 0, 'timestamp >' => time(), 'Tweet.account_id' => $permissions);
        $countConditions2 = array('verified' => 2, 'published' => 0, 'timestamp >' => time(), 'Tweet.account_id' => $permissions);
        //setting the counts on the write tweets page
        if (!empty($filter['user'])) {
            $id = $filter['user'];
            $countConditions0['Tweet.user_id'] = $id;
            $countConditions1['Tweet.user_id'] = $id;
            $countConditions2['Tweet.user_id'] = $id;
        } elseif (!empty($filter['account'])) {
            $id = $twitter_account_id['TwitterAccount']['account_id'];
            $countConditions0['Tweet.account_id'] = $id;
            $countConditions1['Tweet.account_id'] = $id;
            $countConditions2['Tweet.account_id'] = $id;
        }
        $awaitingProofCount = $this->Tweet->find('count', array('conditions' => $countConditions0));
        $queuedCount = $this->Tweet->find('count', array('conditions' => $countConditions1));
        $needImprovingCount = $this->Tweet->find('count', array('conditions' => $countConditions2));
        $this->set('awaitingProofCount', $awaitingProofCount);
        $this->set('queuedCount', $queuedCount);
        $this->set('needImprovingCount', $needImprovingCount);

        
        $toCheck = $this->Paginator->paginate('Tweet');

            $i = 0;
        foreach ($toCheck as $key) {
            //$array = $this->TwitterAccount->find('first', array('fields' => 'screen_name', 'conditions' => array('TwitterAccount.account_id' => $key['Tweet']['account_id'])));
            $screen_name = $key['TwitterAccount']['screen_name'];
            $toCheck[$i]['Tweet']['screen_name'] = $screen_name;
            $i++;
        }
        $this->set('tweets', $toCheck);
    }

    //not used anymore
    /*public function admin() {
        if (isset($this->request->data['currentmonth'])) {
            $this->Session->write('Auth.User.monthSelector', $this->request->data['currentmonth']['Select Month']);
        } elseif ($this->Session->read('Auth.User.monthSelector') == false) {
            $this->Session->write('Auth.User.monthSelector', 0);
        }
        if (isset($this->request->data['accountSubmit'])) {
            $screen_name = $this->request->data['accountSubmit'];
            $new_oauth_tokens = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $screen_name)));
            $this->Session->write('access_token.oauth_token', $new_oauth_tokens[0]['TwitterAccount']['oauth_token']);
            $this->Session->write('access_token.oauth_token_secret', $new_oauth_tokens[0]['TwitterAccount']['oauth_token_secret']);
            $this->Session->write('access_token.account_id', $new_oauth_tokens[0]['TwitterAccount']['account_id']);
            $this->Session->write('access_token.screen_name', $new_oauth_tokens[0]['TwitterAccount']['screen_name']);
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        } else {
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        }


        $tweets = $this->Tweet->find('all', array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id')), 'order' => array('Tweet.timestamp' => 'ASC')));
        $this->set('tweets', $tweets);

        $info = $this->TwitterAccount->find('all', array('fields' => array('infolink'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('info', $info);

        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $teamMembers = $this->User->Team->find('all', array('conditions' => array('Team.id' => $key['id'])));
            $teamMembers[0]['User'][0]['teamName'] = $key['name'];
            $test[] = $teamMembers[0]['User'];
        }
        
        $this->set('teamMembers', $test);
        
        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'order' => array('screen_name' => 'ASC')));
        } else {
            $permissions = array();
            foreach ($this->Session->read('Auth.User.Team') as $key) {
                $permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
                $permissions = array_merge($permissionsx, $permissions);
            }
            $conditions = array('account_id' => $permissions);
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => $conditions, 'order' => array('screen_name' => 'ASC')));
        }
        
        $this->set('accounts', $accounts);
    }*/

    public function calendar($months) {
        if (isset($this->request->data['calendar_activated']['calendar_activated'])) {
            $this->activateCalendar($this->request->data['calendar_activated']['calendar_activated']);
        }
        if (isset($this->request->data['accountSubmit'])) {
            $screen_name = $this->request->data['accountSubmit'];
            $new_oauth_tokens = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $screen_name)));
            $this->Session->write('access_token.oauth_token', $new_oauth_tokens[0]['TwitterAccount']['oauth_token']);
            $this->Session->write('access_token.oauth_token_secret', $new_oauth_tokens[0]['TwitterAccount']['oauth_token_secret']);
            $this->Session->write('access_token.account_id', $new_oauth_tokens[0]['TwitterAccount']['account_id']);
            $this->Session->write('access_token.screen_name', $new_oauth_tokens[0]['TwitterAccount']['screen_name']);
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        } else {
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        }

        if ($this->Session->read('Auth.User.Team.0.id') !== 0) {
            $permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
            $conditions = array('team_id' => $this->Session->read('Auth.User.Team.0.id'));
        } else {
            $conditions = array('TwitterAccount.user_id' => $this->Session->read('Auth.User.id'));
        }
        $calendar = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        $this->set('calendar', $calendar);

        $info = $this->TwitterAccount->find('all', array('fields' => array('infolink'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
        $this->set('info', $info);

        //$teamMembers = $this->User->find('all', array('fields' => array('first_name', 'group_id'), 'conditions' => array('team_id' => $this->Session->read('Auth.User.Team.0.id'))));
        //$this->set('teamMembers', $teamMembers);
        
        if ($this->Session->read('Auth.User.id') == 0 || $this->Session->read('Auth.User.id') == 1) {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'order' => array('screen_name' => 'ASC')));
        } else {
            $accounts = $this->TwitterAccount->find('list', array('fields' => array('screen_name'), 'conditions' => array('account_id' => $permissions), 'order' => array('screen_name' => 'ASC')));
        }
        
        $this->set('accounts', $accounts);

        if ($months) {
            $this->set('months', $months);
        }
    }

    public function connect() {
        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/twitterredirect');

        if ($requestToken) {
            $this->Session->write('twitter_request_token', $requestToken);
            $this->redirect('https://api.twitter.com/oauth/authorize?force_login=true&oauth_token=' . $requestToken->key);
        } else {
            echo 'HELLO';
        }
    }

    public function twitterredirect() {
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);

        $this->Session->write('access_token.oauth_token', $accessToken['oauth_token']);
        $this->Session->write('access_token.oauth_token_secret', $accessToken['oauth_token_secret']);
        $this->Session->write('access_token.screen_name', $accessToken['screen_name']);

        $account = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $accessToken['screen_name'])));
        if (empty($account)) {
            $this->TwitterAccount->create();
            $this->TwitterAccount->save($accessToken);
            $this->TwitterAccount->saveField('user_id', $this->Session->read('Auth.User.id'));
            $this->TwitterAccount->saveField('team_id', $this->Session->read('Auth.User.Team.0.id'));
        } else {
            $id = $account[0]['TwitterAccount']['account_id'];
            unset($accessToken['user_id']);
            $this->TwitterAccount->id = $id;
            $this->TwitterAccount->save($accessToken);
        }
        
            $twitter_account_id = $this->TwitterAccount->getLastInsertId();
            
            $this->Session->write('access_token.account_id', $account[0]['TwitterAccount']['account_id']);
            $twitter_account_id = $account[0]['TwitterAccount']['account_id'];

            $existingPermission = $this->TwitterPermission->find('count', array('conditions' => array('user_id' => $this->Session->read('Auth.User.id'), 'twitter_account_id' => $twitter_account_id, 'team_id' => $this->Session->read('Auth.User.currentTeamId'))));

            if ($existingPermission == 0) {
            $this->TwitterPermission->create();
            $this->TwitterPermission->saveField('user_id', $this->Session->read('Auth.User.id'));
            $this->TwitterPermission->saveField('twitter_account_id', $twitter_account_id);
            $this->TwitterPermission->saveField('team_id', $this->Session->read('Auth.User.currentTeamId'));
            } else {
                $this->Session->setFlash('Account already added to this team');
            }
        //$this->redirect('/twitter/info');
        $this->redirect('/');//REMOVE WHEN REPORTING IS COMPLETE

        //setting database table for reporting archives
        
        /*$modeldate = 'archive_' . strtolower(date('d-M-Y', time() - 60 * 60 * 24 * 1));
        $name = $accessToken['screen_name'];
        $oauth_token = $accessToken['oauth_token'];
        $oauth_token_secret = $accessToken['oauth_token_secret'];
        $account_id = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $name), 'fields' => 'account_id', 'limit' => 1));

        $this->loadModel($modeldate);

            if ($oauth_token&&$oauth_token_secret) {
                $details = json_decode($client->get($oauth_token, $oauth_token_secret, "https://api.twitter.com/1.1/users/show.json?screen_name=$name"), true);
            } else {
                $this->Session->setFlash('Please select an account to tweet from');
            }

            $tweet_rollover = $details['status']['id'] - 1;
            
            $save = array($modeldate => array(
                          'account_id' => $account_id[0]['TwitterAccount']['account_id'],
                          'screen_name' => $name,
                          'followers_count' => $details['followers_count'],
                          'favourites_count' => $details['favourites_count'],
                          'tweet_rollover' => $tweet_rollover));

            $this->$modeldate->save($save);

        $this->redirect('/twitter/');*/

    }

    public function posttweet() {
        $oauth_token = $this->Session->read('access_token.oauth_token');
        $oauth_token_secret = $this->Session->read('access_token.oauth_token_secret');
        $client = $this->createClient();
        debug($this->request->data);

        if ($oauth_token&&$oauth_token_secret) {
            $client->postMultipartFormData($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update_with_media.json', array('media[]' => 'http://guestlist.net/images/logoPage/72dpi/Guestlist_CirclePurple_72dpi.png'), array('status' => 'HEY'));
            $this->Session->setflash('Tweet Sent');
        } else {
            $this->Session->setFlash('Please select an account to tweet from');
        }

        //$this->redirect('/twitter/');
    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }

    public function testing() {//temporary non-verified tweet save
        if ($this->request->data) {
                    //$this->request->data['CronTweet']['time']['hour'] += $this->Session->read('Auth.User.GMT_offset');
                    $toSave['user_id'] = $this->Session->read('Auth.User.id');
                    $toSave['account_id'] = $this->Session->read('access_token.account_id');
                    $toSave['first_name'] = $this->Session->read('Auth.User.first_name');
                    $toSave['verified'] = 0;
                    $toSave['body'] = $this->request->data['Tweet']['body'];
                    $toSave['time'] = $this->request->data['Tweet']['timestamp'];
                    $toSave['timestamp'] = strtotime($this->request->data['Tweet']['timestamp']);

                    if ($this->Tweet->save($toSave)) {
                        
                    } else {
                        $this->Session->setFlash('There was a problem saving your tweet. Please try again.');
                    }
                    $this->redirect(array('action' => 'index'));
        }

        //$this->redirect('/twitter/');
    }

    public function edit() {
        foreach ($this->request->data['Tweet'] as $key) {
            if ($key['id']) {
            $id = $key['id'];
            $this->Tweet->id = $id;
            $this->CronTweet->id = $id;
            $tweet = $this->Tweet->find('first', array('conditions' => array('id' => $id)));
            }

            if ($key['timestamp']) {
            $key['time'] = $key['timestamp'];
            $key['timestamp'] = strtotime($key['timestamp']);
            } else {
            $key['timestamp'] = 0;
            }

            if ($tweet['Tweet']['verified'] == 2 && $key['verified'] == 2) {
                if ($tweet['Tweet']['body'] != $key['body']) {
                    $key['verified'] = 0;
                }
            }

            //$key['first_name'] = $this->Session->read('Auth.User.first_name');

            $key['user_id'] = $this->Session->read('Auth.User.id');
            $key['account_id'] = $this->Session->read('access_token.account_id');
            if ($this->Tweet->save($key)) {
                if ($key['verified'] == 1) {
                    $this->CronTweet->save($key);
                    $this->CronTweet->deleteAll(array('timestamp' => 0));
                }
            } else {
            $this->Session->setFlash('Unable to update your post.');
            }
        }

        $this->redirect(Controller::referer());
    }

    public function emptySave() {
        foreach ($this->request->data['Tweet'] as $key) {
            if ($key['id']) {
            $id = $key['id'];
            $this->Tweet->id = $id;
            $this->CronTweet->id = $id;
            $tweet = $this->Tweet->find('first', array('conditions' => array('id' => $id)));
            }

            if ($key['timestamp']) {
            $key['time'] = $key['timestamp'];
            $key['timestamp'] = strtotime($key['timestamp']);
            } else {
            $key['timestamp'] = 0;
            }

            if ($tweet['Tweet']['verified'] == 2 && $key['verified'] == 2) {
                if ($tweet['Tweet']['body'] != $key['body']) {
                    $key['verified'] = 0;
                }
            }

            //If it's a new tweet OR if you've altered the tweet body
            if (empty($tweet) || $tweet['Tweet']['body'] != $key['body']) {
                $key['user_id'] = $this->Session->read('Auth.User.id');
                $key['verified'] = 0;
            }

            //Handling images
            if (!empty($key['img_url1']['name'])) {
                if ($key['img_url1']['error'] == 0) {
                    $z = explode(".", $key['img_url1']['name']);
                    $extension = end($z);
                    $allowed_extensions = array("gif", "jpeg", "jpg", "png");
    
                    if (in_array($extension, $allowed_extensions)) {
                        $newFileName = $this->Session->read('Auth.User.id') . md5(time()) . "." . $extension;
                        move_uploaded_file($key['img_url1']['tmp_name'], '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName);
                        $key['img_url'] = "http://social.guestlist.net/img/uploads/".$newFileName;
                    }            
                }
            } else {
                $key['img_url'] = $tweet['Tweet']['img_url'];
            }

            if ($this->Tweet->saveField('body', $key['body'])) {
                $this->Tweet->saveField('verified', $key['verified']);
                $this->Tweet->saveField('comments', $key['comments']);
                if (isset($key['img_url'])) {
                    $this->Tweet->saveField('img_url', $key['img_url']);
                }
                if ($key['verified'] == 1) {
                    $this->CronTweet->save($key);
                    $this->CronTweet->deleteAll(array('timestamp' => 0));
                }
            } else {
            $this->Session->setFlash('Unable to update your post.');
            }
            unset($key);
        }

        $this->redirect(Controller::referer());
        
    }

    public function delete($id) {
        if($this->Tweet->delete($id)) {
            $this->CronTweet->delete($id);
            $this->Session->setFlash('Tweet has been deleted.');
            $this->redirect('/');
        }
    }

    public function tablerefresh() {
        $this->Paginator->settings = array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name', 'verified_by'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id')), 'order' => array('Tweet.timestamp' => 'ASC'));
        $tweets = $this->Paginator->paginate('Tweet');
        $this->set('tweets', $tweets);
        $this->layout = '';
    }

    public function indexrefresh() {
        $permissions = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            $permissions = array_merge($permissionsx, $permissions);
            $myteam[] = $key['id'];
        }
        /*
        $v = 0;
        $p = 0;
        $t = time();
        $order = 'asc';
        $this->set('params', '');
        if (isset($this->request->query['h'])) {
            if ($this->request->query['h'] == 'queued') {
                $v = 1;
                $p = 0;
                $this->set('params', 'h:queued');
            } elseif ($this->request->query['h'] == 'published') {
                $v = 1;
                $p = 1;
                $t = -1;
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->request->query['h'] == 'improving') {
                $v = 2;
                $p = 0;
                $this->set('params', 'h:improving');
            } elseif ($this->request->query['h'] == 'notpublished') {
                $v = array(0, 2);
                $p = 0;
                $timestamp = 'timestamp <';
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->request->query['h'] == 'daybyday') {
                $this->set('params', 'h:daybyday');
            }
        }

        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $myteam[] = $key['id'];
        }

        if ($this->Session->read('filterUser')) {
            $written_by = $this->User->find('first', array('fields' => 'first_name', 'conditions' => array('User.id' => $this->Session->read('filterUser'))));
            $c = array('verified' => $v, 'published' => $p, 'timestamp >' => $t, 'first_name' => $written_by['User']['first_name']);
        } elseif ($this->Session->read('filterAccount')) {
            $twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => 'account_id', 'conditions' => array('screen_name' => $this->Session->read('filterAccount'))));
            $c = array('verified' => $v, 'published' => $p, 'timestamp >' => $t, 'account_id' => $twitter_account_id['TwitterAccount']['account_id']);
        } else {
            $c = array('verified' => $v, 'published' => $p, 'timestamp >' => $t, 'account_id' => $permissions);
        }*/


        $v = array(0, 1, 2);
        $p = 0;
        $t = time();
        $timestamp = 'timestamp >';
        $order = 'asc';
        $twitter_account_id['TwitterAccount']['account_id'] = $permissions;
        $this->set('status', '');
        $this->set('user', '');
        $this->set('account', '');
        $this->set('team', '');
        if (!empty($this->request->query['h'])) {
            if ($this->request->query['h'] == 'daybyday') {
                $this->set('params', 'h:daybyday');
            }
        } else {
            $this->set('params', '');
        }
        
            $filter = $this->Session->read('filter');
        if (!empty($filter)) {
            if (!empty($filter['status'])) {
                if ($filter['status'] == 'queued') {
                    $v = 1;
                    $this->set('status', 'queued');
                } elseif ($filter['status'] == 'awaitingproof') {
                    $v = 0;
                    $this->set('status', 'awaitingproof');
                } elseif ($filter['status'] == 'improving') {
                    $v = 2;
                    $this->set('status', 'improving');
                } elseif ($filter['status'] == 'published') {
                    $p = 1;
                    $t = -1;
                    $order = 'desc';
                    $this->set('status', 'published');
                } elseif ($filter['status'] == 'notpublished') {
                    $v = array(0, 2);
                    $p = 0;
                    $timestamp = 'timestamp <';
                    $order = 'desc';
                    $this->set('status', 'notpublished');
                }
            }

            if (!empty($filter['user'])) {
                $user_id = $filter['user'];
                $this->set('user', $user_id);
            }

            if (!empty($filter['account'])) {
                $twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => array('account_id', 'screen_name'), 'conditions' => array('screen_name' => $filter['account'])));
                $this->Session->write('access_token.account_id', $twitter_account_id['TwitterAccount']['account_id']);
                $this->Session->write('access_token.screen_name', $twitter_account_id['TwitterAccount']['screen_name']);
                $this->set('account', $filter['account']);
            }

            if (!empty($filter['team'])) {
                $twitter_account_id['TwitterAccount']['account_id'] = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $filter['team'])));
                $this->set('team', $filter['team']);
            }
        }

        if (!empty($user_id)) {
            $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'Tweet.account_id' => $twitter_account_id['TwitterAccount']['account_id'], 'Tweet.user_id' => $user_id);
        } else {
            $c = array('verified' => $v, 'published' => $p, $timestamp => $t, 'Tweet.account_id' => $twitter_account_id['TwitterAccount']['account_id']);
        }

        $this->Paginator->settings = array(
        'conditions' => $c,
        'limit' => 10,
        'order' => array('timestamp' => $order)
        );

        
        $toCheck = $this->Paginator->paginate('Tweet');

            $i = 0;
        foreach ($toCheck as $key) {
            $screen_name = $key['TwitterAccount']['screen_name'];
            $toCheck[$i]['Tweet']['screen_name'] = $screen_name;
            $i++;
        }
        $this->set('tweets', $toCheck);
        $this->layout = '';
    }

    public function info() {
        if ($this->request->data) {
            $id = $this->Session->read('access_token.account_id');
            $this->TwitterAccount->id = $id;
            $info = $this->request->data['TwitterAccount']['infolink'];
            $this->TwitterAccount->saveField('infolink', $info);

            $this->redirect('/twitter/');
        }
    }

    private function activateCalendar($data) {
        if ($this->Session->read('Auth.User.Team.0.id') == 0) {
            $conditions = array('id' => $this->Session->read('Auth.User.id'));
        } else {
            $conditions = array('team_id' => $this->Session->read('Auth.User.Team.0.id'));
        }
        $users = $this->User->find('list', array('conditions' => $conditions, 'fields' => 'id'));

        foreach ($users as $key) {
            $this->User->id = $key;
            $this->User->savefield('calendar_activated', $data);
        }
        
        $this->Session->write('Auth.User.calendar_activated', $data);
        if ($data == 1) {
            $this->Session->setFlash('Editorial Calendars have been activated for you team. Your team will now see them on the main page.');
        } elseif ($data == 0) {
            $this->Session->setFlash('Editorial Calendars deactivated.');
        }
    }

    public function deleteImage($tweet_id) {
        $this->Tweet->id = $tweet_id;
        $this->CronTweet->id = $tweet_id;
        $this->Tweet->saveField('img_url', '');
        $this->CronTweet->saveField('img_url', '');

        $this->redirect(Controller::referer());
    }

    public function progressrefresh($daybyday = null, $months = 0) {
        $permissions = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            $permissions = array_merge($permissionsx, $permissions);
        }


        $countConditions0 = array('verified' => 0, 'published' => 0, 'timestamp >' => time(), 'Tweet.account_id' => $permissions);
        $countConditions1 = array('verified' => 1, 'published' => 0, 'timestamp >' => time(), 'Tweet.account_id' => $permissions);
        $countConditions2 = array('verified' => 2, 'published' => 0, 'timestamp >' => time(), 'Tweet.account_id' => $permissions);
        //setting the counts on the write tweets page
        if ($this->Session->read('filter.user')) {
            $id = $this->Session->read('filter.user');
            $countConditions0['Tweet.user_id'] = $id;
            $countConditions1['Tweet.user_id'] = $id;
            $countConditions2['Tweet.user_id'] = $id;
        } elseif ($this->Session->read('filter.account')) {
            $id = $this->Session->read('access_token.account_id');
            $countConditions0['Tweet.account_id'] = $id;
            $countConditions1['Tweet.account_id'] = $id;
            $countConditions2['Tweet.account_id'] = $id;
        }

        if ($months != 0) {
            $countConditions0['timestamp >='] = strtotime(date('M Y') . ' + ' . ($months) . 'months');
            $countConditions1['timestamp >='] = strtotime(date('M Y') . ' + ' . ($months) . 'months');
            $countConditions2['timestamp >='] = strtotime(date('M Y') . ' + ' . ($months) . 'months');
        }
        if (!empty($daybyday)) {
            $countConditions0['timestamp <='] = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
            $countConditions1['timestamp <='] = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
            $countConditions2['timestamp <='] = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
        }
        $awaitingProofCount = $this->Tweet->find('count', array('conditions' => $countConditions0));
        $queuedCount = $this->Tweet->find('count', array('conditions' => $countConditions1));
        $needImprovingCount = $this->Tweet->find('count', array('conditions' => $countConditions2));
        $this->set('awaitingProofCount', $awaitingProofCount);
        $this->set('queuedCount', $queuedCount);
        $this->set('needImprovingCount', $needImprovingCount);

        $this->layout = '';
    }

    public function approveall() {
        $twitter_account_id = $this->request->data['account_id'];
        $months = $this->request->data['month'];

        $calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $twitter_account_id)));
        $calendarIDs = array();
        foreach ($calendars as $key) {
            $calendarIDs[] = $key['EditorialCalendar']['id'];
        }

        if ($month = 0) {
            $firstdate = time();
        } else {
            $firstdate = strtotime(date('M Y') . ' + ' . ($months) . 'months');
        }
        $tweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => $twitter_account_id, 'calendar_id' => $calendarIDs, 'timestamp >=' => $firstdate, 'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months'))));
        $toSave = array();
        foreach ($tweets as $key) {
            $x['verified'] = 1;
            $x['id'] = $key['Tweet']['id'];
            $toSave[] = $x;
        }
        if ($this->Tweet->saveAll($toSave)) {
            $this->Session->setFlash('yes');
        } else {
            $this->Session->setFlash('no');
        }

        $this->redirect(Controller::referer());
    }

    public function test($id) {
        /*$Email = new CakeEmail();
        $Email->from(array('registration@social.guestlist.net' => 'Guestlist Social'));
        $Email->to("sharif9876@hotmail.com");
        $Email->subject('TEST');
        debug($Email->send('THIS IS A TEST'));*/
        //$this->Notification->add(1, 'A comment was added to your tweet', 0);
        //$this->Notification->add(1, 'A comment was added to your tweet1', 1);
        //$this->Notification->add(1, 'A comment was added to your tweet2', 0);
        //$this->Notification->markAsRead(1);
        //debug($this->Tweet->find('first', array('conditions' => array('Tweet.id' => 52826), 'recursive' => 2)));
        $tweet = $this->Tweet->find('first', array('conditions' => array('Tweet.id' => $id)));
        $calendarID = $tweet['Tweet']['calendar_id'];
        $calendar = $this->EditorialCalendar->find('first', array('conditions' => array('id' => $calendarID)));
        //debug($calendar);
        foreach ($calendar['Tweet'] as $key) {
            $date = date('F Y', $key['timestamp']);
            $test[$date][] = $key;
        }

        foreach ($test as $key => $value) {
            echo '<br />' . $key . ': <br />';
            foreach ($value as $key1) {
                echo $key1['body'] . '<br />';
            }
        }
    }
}