<?php
class TweetBankController extends AppController {
	public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form', 'Session');
    var $uses = array('Tweet', 'Notification', 'Comment', 'BankCategory', 'TwitterPermission', 'EditorialCalendar', 'EditorialCalendar1', 'TweetBank', 'TwitterAccount');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    public function index() {
    	$teamIDs = array();
    	foreach ($this->Session->read('Auth.User.Team') as $key) {
    		$teamIDs[] = $key['id'];
    	}
    	$accounts = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $teamIDs)));
    	$screen_names = $this->TwitterAccount->find('list', array('fields' => 'screen_name', 'conditions' => array('account_id' => $accounts), 'order' => array('screen_name' => 'ASC')));
    	$this->set('accounts', $screen_names);
        
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

    	$categories = $this->BankCategory->find('all', array('conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
    	if (!empty($categories)) {
	    	foreach ($categories as $key) {
	    		$categoriesx[$key['BankCategory']['id']] = $key['BankCategory']['category'];
	    	}
    	} else {
    		$categoriesx = '';
    	}
    	$this->set('categories', $categoriesx);
    	if (!empty($this->request->data['BankCategory'])) {
    		$this->set('selectedCategories', $this->request->data['BankCategory']);
	    	$tweets = $this->TweetBank->find('all', array('conditions' => array('account_id' => $this->Session->read('access_token.account_id'), 'bank_category_id' => $this->request->data['BankCategory'])));

    	} else {
    		$this->set('selectedCategories', '');
	    	$tweets = $this->TweetBank->find('all', array('conditions' => array('account_id' => $this->Session->read('access_token.account_id'))));
    	}

	   	$this->set('tweets', $tweets);
    }

    public function index3($account_id) {
    	/*$teamIDs = array();
    	foreach ($this->Session->read('Auth.User.Team') as $key) {
    		array_push($teamIDs, $key['id']);
    	}
    	$permissions = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $teamIDs)));
    	debug($this->BankCategory->find('all', array('conditions' => array('account_id' => $permissions))));
    	$tweets = $this->Tweet->find('all', array('conditions' => array('calendar_id' => 1)));
    	$toSave = array();
    	foreach ($tweets as $key) {
    		$x['TweetBank']['bank_category_id'] = $key['EditorialCalendar']['bank_category_id'];
    		$x['TweetBank']['body'] = $key['Tweet']['body'];
    		$day = strtolower(date('l', $key['Tweet']['timestamp']));
    		//$x['BankCategory']['category'] = $key['EditorialCalendar'][$day . '_topic'];
    		//$x['BankCategory']['account_id'] = $key['TwitterAccount']['account_id'];
    		$toSave[] = $x;
    	}*/


    	//create new editorial calendars by account id
    	$toSave = array();
    	$calendars = $this->EditorialCalendar1->find('all', array('conditions' => array('twitter_account_id' => array($account_id))));
    	foreach ($calendars as $key) {
    		$array = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    		foreach ($array as $key1) {
    			$x['EditorialCalendar']['category'] = trim($key['EditorialCalendar1'][$key1 . '_topic']);
    			$x['EditorialCalendar']['day'] = $key1;
    			$x['EditorialCalendar']['time'] = date('H:i', strtotime($key['EditorialCalendar1']['time']));
    			$x['EditorialCalendar']['twitter_account_id'] = $key['EditorialCalendar1']['twitter_account_id'];
    			$toSave[] = $x;
    		}
    	}
    	$this->EditorialCalendar->saveAll($toSave);
    	unset($toSave);
    	unset($x);
    	unset($calendars);
    	unset($key);


    	//create new bank categories for an account_id
    	$calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => array($account_id))));
    	foreach ($calendars as $key) {
    			if (!empty($key['EditorialCalendar']['category'])) {
					$x['BankCategory']['category'] = trim($key['EditorialCalendar']['category']);
					$x['BankCategory']['account_id'] = $account_id;
					$toSave1[strtolower($x['BankCategory']['category']) . $x['BankCategory']['account_id']] = $x;
    			}
    	}
    	foreach ($toSave1 as $key2) {
    		$toSave[] = $key2;
    	}
    	debug($toSave);
    	$this->BankCategory->saveAll($toSave);
    	unset($toSave);
    	unset($toSave1);
    	unset($x);
    	unset($calendars);
    	unset($key);



    	//grab bank_category_id for new editorial calendars
    	$calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => array($account_id))));
    	$toSave = array();
    	foreach ($calendars as $key) {
    		$calendarcategories[$key['EditorialCalendar']['id']] = $key['EditorialCalendar']['category'];
    	}
    	$BankCategory = $this->BankCategory->find('all', array('conditions' => array('category' => $calendarcategories, 'account_id' => $account_id)));
    	foreach ($BankCategory as $key) {
    		$bankcategories[$key['BankCategory']['id']] = $key['BankCategory']['category'];
    	}
    	foreach ($calendarcategories as $key => $value) {
    		if (!empty(array_flip($bankcategories)[$value])) {
	    		$x['EditorialCalendar']['id'] = $key;
	    		$x['EditorialCalendar']['bank_category_id'] = array_flip($bankcategories)[$value];
	    		$toSave[] = $x;
    		}
    	}
    	$this->EditorialCalendar->saveAll($toSave);
    	unset($toSave);
    	unset($x);
    	unset($calendars);
    	unset($key);
    	unset($calendarcategories);
    	unset($bankcategories);


    	//get new calendar_ids for tweets given account and dates
    	$months = 0;
    	$firstdate = strtotime(date('M Y') . ' + ' . ($months) . 'months');
    	$seconddate = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
    	$tweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => $account_id, 'timestamp >' => $firstdate, 'timestamp <' => $seconddate)));
    	$accounts = array();
    	$days = array();
    	$times = array();
    	foreach ($tweets as $key) {
    		$id = $key['Tweet']['id'];
    		$account = $key['Tweet']['account_id'];
    		$day = strtolower(date('l', $key['Tweet']['timestamp']));
    		$time = date('H:i', $key['Tweet']['timestamp']);

    		$accounts[] = $account;
    		$days[] = $day;
    		$times[] = $time;
    	}
    	$calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $accounts, 'day' => $days, 'time' => $times)));
    	$calendarscomparison = array();
    	foreach ($calendars as $key) {
    		$id = $key['EditorialCalendar']['id'];
    		$day = $key['EditorialCalendar']['day'];
    		$time = $key['EditorialCalendar']['time'];
    		$account_id = $key['EditorialCalendar']['twitter_account_id'];

    		$calendarscomparison[$account_id . $day . $time] = $id;
    	}

    	$toSave = array();
    	foreach ($tweets as $key) {
    		$id = $key['Tweet']['id'];
    		$account = $key['Tweet']['account_id'];
    		$day = strtolower(date('l', $key['Tweet']['timestamp']));
    		$time = date('H:i', $key['Tweet']['timestamp']);
    		if (!empty($calendarscomparison[$account . $day . $time])) {
	    		$calendar_id = $calendarscomparison[$account . $day . $time];
	    		$x['Tweet']['id'] = $id;
	    		$x['Tweet']['calendar_id'] = $calendar_id;
	    		$toSave[] = $x;
    		}
    	}
    	$this->Tweet->saveAll($toSave);
    	unset($toSave);
    	unset($x);
    	unset($tweets);
    	unset($key);

    	//create tweet_banks for tweets for a given month an given account
    	$months = 0;
    	$firstdate = strtotime(date('M Y') . ' + ' . ($months) . 'months');
    	$seconddate = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
    	$tweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => $account_id, 'timestamp >' => $firstdate, 'timestamp <' => $seconddate, 'calendar_id <>' => 0)));
    	$toSave = array();
    	foreach ($tweets as $key) {
    		if ($key['EditorialCalendar']['bank_category_id'] != 0) {
    			$x['Tweet']['id'] = $key['Tweet']['id'];
	    		$x['TweetBank']['body'] = $key['Tweet']['body'];
	    		$x['TweetBank']['bank_category_id'] = $key['EditorialCalendar']['bank_category_id'];
	    		$x['TweetBank']['img_url'] = $key['Tweet']['img_url'];
	    		$toSave[] = $x;
    		}
    	}
    	debug($toSave);
    	$this->Tweet->saveAll($toSave, array('deep' => true));
    	debug($this->TweetBank->validationErrors);
    }

    public function save() {
    	$toSave = array();
    	debug($this->request->data);
    	foreach ($this->request->data['TweetBank'] as $key => $value) {
			$x['TweetBank']['bank_category_id'] = $value['bank_category_id'];
			$x['TweetBank']['body'] = $value['body'];
    		if ($key == 'new') {
    			if ($value['bank_category_id'] == '' || $value['body'] == '') {
    				unset($x);
    			}
    		} else {
    			$x['TweetBank']['id'] = $key;
    		}

    		if (!empty($value['img_url1'])) {
    			if ($xx = $this->imageHandling($value)) {
                        $value['img_url'] = $xx;
                        $x['TweetBank']['img_url'] = $value['img_url'];
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
    		}

    		if (!empty($x)) {
				$toSave[] = $x;
				unset($x);
    		}
    	}
    	
    	if ($this->TweetBank->saveAll($toSave)) {
    		$this->response->statusCode(200);
    	} else {
    		$this->response->statusCode(500);
    	}
    	return $this->response;
    	$this->redirect(Controller::referer());
    }

    private function imageHandling($key) {
        debug($key['img_url1']['name']);
        if ($key['img_url1']['error'] == 0) {
            $z = explode(".", $key['img_url1']['name']);
            $extension = end($z);
            $allowed_extensions = array("gif", "jpeg", "jpg", "png");
        
            if (in_array(strtolower($extension), $allowed_extensions)) {
                $newFileName = $this->Session->read('Auth.User.id') . "-" . $this->Session->read('access_token.account_id') . "-" . $key['bank_category_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                move_uploaded_file($key['img_url1']['tmp_name'], '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName);

                //delete current image
                /*if (!empty($key['img_url'])) {
                    $toDelete = str_replace("http://social.guestlist.net/", '', $key['img_url']);
                    unlink($toDelete);
                }*/
                $key['img_url'] = "http://social.guestlist.net/img/uploads/".$newFileName;
                return $key['img_url'];
            } else {
                $this->Session->setFlash('You can only upload images.');
                return false;
            }
    
        } elseif ($key['img_url1']['error'] == 1) {
            $this->Session->setFlash('Image too large, please try another image (Max 2MB)');
            return false;
        } else {
            return false;
        }

    }

    public function delete($id) {
    	$tweet = $this->TweetBank->find('first', array('conditions' => array('TweetBank.id' => $id)));
    	$account = $this->TwitterAccount->find('first', array('conditions' => array('account_id' => $tweet['BankCategory']['account_id'])));
    	debug($tweet);
    	$teamIDs = array();
    	foreach ($this->Session->read('Auth.User.Team') as $key) {
    		if ($key['TeamsUser']['group_id'] == 1) {
    			$teamIDs[] = $key['id'];
    		}
    	}
    	debug($teamIDs);
    	debug($account);
    	if ($this->TwitterPermission->hasAny(array('twitter_account_id' => $account['TwitterAccount']['account_id'], 'team_id' => $teamIDs))) {
    		$tweets = $this->Tweet->find('all', array('conditions' => array('tweet_bank_id' => $id)));
    		$toSave = array();
    		foreach ($tweets as $key) {
    			$x['id'] = $key['id'];
    			$x['tweet_bank_id'] = 0;
    			$toSave[] = $x;
    		}
    		$this->Tweet->saveAll($toSave);
    		$this->TweetBank->delete($id);
    		if ($this->TweetBank->delete($id)) {
                $this->response->statusCode(200);
            } else {
                $this->response->statusCode(500);
            }
    	} else {
    		$this->response->statusCode(500);
    	}

		return $this->response;
    	$this->redirect(Controller::referer());
    }

    public function test() {
    	debug(strtotime('10-08-2015 11:00'));
    	debug(strtotime('11-08-2015 11:00'));
    }
}