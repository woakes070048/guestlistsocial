<?php
App::import('Vendor', 'OAuth/OAuthClient');

class TwitterController extends AppController {
    public $components = array('Session', 'Auth', 'Paginator', 'Tickets');
    public $helpers =  array('Html' , 'Form');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar', 'Ticket', 'TeamsUser', 'Team');

    public function index() {
        if (isset($this->request->data['currentmonth'])) {
            $this->Session->write('Auth.User.monthSelector', $this->request->data['currentmonth']['Select Month']);
        } elseif ($this->Session->read('Auth.User.monthSelector') == false) {
            $this->Session->write('Auth.User.monthSelector', 0);
        }

        $permissions = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            $permissions = array_merge($permissionsx, $permissions);
            $myteam[] = $key['id'];
        }
        $conditions = array('account_id' => $permissions);

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

        $v = 0;
        $p = 0;
        $t = time();
        $timestamp = 'timestamp >';
        $order = 'asc';
        $this->set('params', '');
        if (isset($this->passedArgs['h'])) {
            if ($this->passedArgs['h'] == 'queued') {
                $v = 1;
                $p = 0;
                $this->set('params', 'h:queued');
            } elseif ($this->passedArgs['h'] == 'published') {
                $v = 1;
                $p = 1;
                $t = -1;
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->passedArgs['h'] == 'improving') {
                $v = 2;
                $p = 0;
                $this->set('params', 'h:improving');
            } elseif ($this->passedArgs['h'] == 'notpublished') {
                $v = array(0, 2);
                $p = 0;
                $timestamp = 'timestamp <';
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->passedArgs['h'] == 'daybyday') {
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

        }

        $this->Paginator->settings = array(
        'conditions' => $c,
        'limit' => 10,
        'order' => array('timestamp' => $order)
        );

        $countConditions0 = array('verified' => 0, 'published' => 0, 'timestamp >' => time(), 'account_id' => $permissions);
        $countConditions1 = array('verified' => 1, 'published' => 0, 'timestamp >' => time(), 'account_id' => $permissions);
        $countConditions2 = array('verified' => 2, 'published' => 0, 'timestamp >' => time(), 'account_id' => $permissions);
        //setting the counts on the write tweets page
        if ($this->Session->read('filterUser')) {
            $id = $this->Session->read('filterUser');
            $countConditions0['user_id'] = $id;
            $countConditions1['user_id'] = $id;
            $countConditions2['user_id'] = $id;
        } elseif ($this->Session->read('filterAccount')) {
            $id = $twitter_account_id['TwitterAccount']['account_id'];
            $countConditions0['account_id'] = $id;
            $countConditions1['account_id'] = $id;
            $countConditions2['account_id'] = $id;
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
            $array = $this->TwitterAccount->find('first', array('fields' => 'screen_name', 'conditions' => array('account_id' => $key['Tweet']['account_id'])));
            $toCheck[$i]['Tweet']['screen_name'] = $array['TwitterAccount']['screen_name'];
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
            $conditions = array('user_id' => $this->Session->read('Auth.User.id'));
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

        $count = $this->TwitterAccount->find('count', array('conditions' => array('screen_name' => $accessToken['screen_name'])));
        if ($count == 0) {
            $this->TwitterAccount->create();
            $this->TwitterAccount->save($accessToken);
            $this->TwitterAccount->saveField('user_id', $this->Session->read('Auth.User.id'));
            $this->TwitterAccount->saveField('team_id', $this->Session->read('Auth.User.Team.0.id'));
            $twitter_account_id = $this->TwitterAccount->getLastInsertId();
        } else {

        }
        
            
            
            $account = $this->TwitterAccount->find('all', array('conditions' => array('screen_name' => $accessToken['screen_name'])));
            $this->Session->write('access_token.account_id', $account[0]['TwitterAccount']['account_id']);
            $twitter_account_id = $account[0]['TwitterAccount']['account_id'];

            $this->TwitterPermission->create();
            $this->TwitterPermission->saveField('user_id', $this->Session->read('Auth.User.id'));
            $this->TwitterPermission->saveField('twitter_account_id', $twitter_account_id);
            $this->TwitterPermission->saveField('team_id', $this->Session->read('Auth.User.currentTeamId'));
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

                    $this->Tweet->create();
                    $this->Tweet->save($toSave);
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

            if ($this->Tweet->saveField('verified', $key['verified'])) {
                $this->Tweet->saveField('body', $key['body']);
                if ($key['verified']) {
                    $this->Tweet->saveField('verified_by', $key['verified_by']);
                }
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
            $this->redirect(array('action' => 'admin'));
        }
    }

    public function tablerefresh() {
        $this->Paginator->settings = array('fields' => array('id', 'body', 'verified', 'client_verified', 'time', 'published', 'first_name', 'verified_by'), 'conditions' => array('account_id' => $this->Session->read('access_token.account_id')), 'order' => array('Tweet.timestamp' => 'ASC'));
        $tweets = $this->Paginator->paginate('Tweet');
        $this->set('tweets', $tweets);
        $this->layout = '';
    }

    public function indexrefresh() {
        $v = 0;
        $p = 0;
        $t = time();
        $order = 'asc';
        $this->set('params', '');
        if (isset($this->passedArgs['h'])) {
            if ($this->passedArgs['h'] == 'queued') {
                $v = 1;
                $p = 0;
                $this->set('params', 'h:queued');
            } elseif ($this->passedArgs['h'] == 'published') {
                $v = 1;
                $p = 1;
                $t = -1;
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->passedArgs['h'] == 'improving') {
                $v = 2;
                $p = 0;
                $this->set('params', 'h:improving');
            } elseif ($this->passedArgs['h'] == 'notpublished') {
                $v = array(0, 2);
                $p = 0;
                $timestamp = 'timestamp <';
                $order = 'desc';
                $this->set('params', 'h:published');
            } elseif ($this->passedArgs['h'] == 'daybyday') {
                $this->set('params', 'h:daybyday');
            }
        }

        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $myteam[] = $key['id'];
        }

        if ($this->Session->read('filterUser')) {
            $written_by = $this->User->find('first', array('fields' => 'first_name', 'conditions' => array('User.id' => $this->Session->read('filterUser'))));
            $c = array('team_id' => $myteam, 'verified' => $v, 'published' => $p, 'timestamp >' => $t, 'first_name' => $written_by['User']['first_name']);
        } elseif ($this->Session->read('filterAccount')) {
            $twitter_account_id =  $this->TwitterAccount->find('first', array('fields' => 'account_id', 'conditions' => array('screen_name' => $this->Session->read('filterAccount'))));
            $c = array('team_id' => $myteam, 'verified' => $v, 'published' => $p, 'timestamp >' => $t, 'account_id' => $twitter_account_id['TwitterAccount']['account_id']);
        } else {
            $c = array('team_id' => $myteam, 'verified' => $v, 'published' => $p, 'timestamp >' => $t);
        }

        $this->Paginator->settings = array(
        'conditions' => $c,
        'limit' => 10,
        'order' => array('timestamp' => $order)
        );

        
        $toCheck = $this->Paginator->paginate('Tweet');

            $i = 0;
        foreach ($toCheck as $key) {
            $array = $this->TwitterAccount->find('first', array('fields' => 'screen_name', 'conditions' => array('account_id' => $key['Tweet']['account_id'])));
            $toCheck[$i]['Tweet']['screen_name'] = $array['TwitterAccount']['screen_name'];
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

    public function progressrefresh() {
        $permissions = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $permissionsx = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $key['id'])));
            $permissions = array_merge($permissionsx, $permissions);
        }


        $countConditions0 = array('verified' => 0, 'published' => 0, 'timestamp >' => time(), 'account_id' => $permissions);
        $countConditions1 = array('verified' => 1, 'published' => 0, 'timestamp >' => time(), 'account_id' => $permissions);
        $countConditions2 = array('verified' => 2, 'published' => 0, 'timestamp >' => time(), 'account_id' => $permissions);
        //setting the counts on the write tweets page
        if ($this->Session->read('filterUser')) {
            $id = $this->Session->read('filterUser');
            $countConditions0['user_id'] = $id;
            $countConditions1['user_id'] = $id;
            $countConditions2['user_id'] = $id;
        } elseif ($this->Session->read('filterAccount')) {
            $id = $this->Session->read('access_token.account_id');
            $countConditions0['account_id'] = $id;
            $countConditions1['account_id'] = $id;
            $countConditions2['account_id'] = $id;
        }
        $awaitingProofCount = $this->Tweet->find('count', array('conditions' => $countConditions0));
        $queuedCount = $this->Tweet->find('count', array('conditions' => $countConditions1));
        $needImprovingCount = $this->Tweet->find('count', array('conditions' => $countConditions2));
        $this->set('awaitingProofCount', $awaitingProofCount);
        $this->set('queuedCount', $queuedCount);
        $this->set('needImprovingCount', $needImprovingCount);

        $this->layout = '';
    }

    public function test() {
        $Email = new CakeEmail();
        $Email->from(array('registration@social.guestlist.net' => 'Guestlist Social'));
        $Email->to("sharif9876@hotmail.com");
        $Email->subject('TEST');
        debug($Email->send('THIS IS A TEST'));
    }
}