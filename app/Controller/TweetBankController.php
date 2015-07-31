<?php
class TweetBankController extends AppController {
	public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form', 'Session');
    var $uses = array('Tweet', 'Notification', 'Comment', 'BankCategory', 'TwitterPermission', 'EditorialCalendar', 'EditorialCalendar1', 'TweetBank');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    public function index() {

    }

    public function index3() {
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
    	/*$toSave = array();
    	$calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => array(1))));
    	foreach ($calendars as $key) {
    		$array = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    		foreach ($array as $key1) {
    			$x['EditorialCalendar1']['category'] = trim($key['EditorialCalendar'][$key1 . '_topic']);
    			$x['EditorialCalendar1']['day'] = $key1;
    			$x['EditorialCalendar1']['time'] = date('H:i', strtotime($key['EditorialCalendar']['time']));
    			$x['EditorialCalendar1']['twitter_account_id'] = $key['EditorialCalendar']['twitter_account_id'];
    			$toSave[] = $x;
    		}
    	}
    	debug($toSave);
    	$this->EditorialCalendar1->saveAll($toSave);*/


    	//create new bank categories for an account_id
    	/*$calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => array(1))));
    	foreach ($calendars as $key) {
    			if (!empty($key['EditorialCalendar']['category'])) {
					$x['BankCategory']['category'] = trim($key['EditorialCalendar']['category']);
					$x['BankCategory']['account_id'] = $key['EditorialCalendar']['twitter_account_id'];
					$toSave1[$x['BankCategory']['category'] . $x['BankCategory']['account_id']] = $x;
    			}
    	}
    	foreach ($toSave1 as $key2) {
    		$toSave[] = $key2;
    	}
    	$toSave = array(
		'BankCategory' => array(
			'category' => 'Motivational quote',
			'account_id' => '1'
		));
    	$this->BankCategory->saveAll($toSave);*/



    	//grab bank_category_id for new editorial calendars
    	/*$calendars = $this->EditorialCalendar1->find('all', array('conditions' => array('twitter_account_id' => array(1))));
    	$toSave = array();
    	foreach ($calendars as $key) {
    		$calendarcategories[$key['EditorialCalendar1']['id']] = $key['EditorialCalendar1']['category'];
    	}
    	$BankCategory = $this->BankCategory->find('all', array('conditions' => array('category' => $calendarcategories, 'account_id' => 1)));
    	foreach ($BankCategory as $key) {
    		$bankcategories[$key['BankCategory']['id']] = $key['BankCategory']['category'];
    	}
    	foreach ($calendarcategories as $key => $value) {
    		if (!empty(array_flip($bankcategories)[$value])) {
	    		$x['EditorialCalendar1']['id'] = $key;
	    		$x['EditorialCalendar1']['bank_category_id'] = array_flip($bankcategories)[$value];
	    		$toSave[] = $x;
    		}
    	}
    	$this->EditorialCalendar1->saveAll($toSave);*/


    	//get new calendar_ids for tweets given account and dates
    	/*$months = 0;
    	$firstdate = strtotime(date('M Y') . ' + ' . ($months) . 'months');
    	$seconddate = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
    	$tweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => 1, 'timestamp >' => $firstdate, 'timestamp <' => $seconddate)));
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

    		$calendar_id = $calendarscomparison[$account . $day . $time];
    		$x['Tweet']['id'] = $id;
    		$x['Tweet']['calendar_id'] = $calendar_id;
    		$toSave[] = $x;
    	}
    	$this->Tweet->saveAll($toSave);*/

    	//create tweet_banks for tweets for a given month an given account
    	/*$months = 0;
    	$firstdate = strtotime(date('M Y') . ' + ' . ($months) . 'months');
    	$seconddate = strtotime(date('M Y') . ' + ' . ($months + 1) . 'months');
    	$tweets = $this->Tweet->find('all', array('conditions' => array('Tweet.account_id' => 1, 'timestamp >' => $firstdate, 'timestamp <' => $seconddate, 'calendar_id <>' => 0)));
    	$toSave = array();
    	foreach ($tweets as $key) {
    		if ($key['EditorialCalendar']['bank_category_id'] != 0) {
	    		$x['TweetBank']['body'] = $key['Tweet']['body'];
	    		$x['TweetBank']['bank_category_id'] = $key['EditorialCalendar']['bank_category_id'];
	    		$toSave[] = $x;
    		}
    	}
    	debug($toSave);
    	$this->TweetBank->saveAll($toSave);
    	debug($this->TweetBank->validationErrors);*/
    	debug(utf8_decode("All the money thrown into â€ª#â€ŽRomeâ€¬'s â€ª#â€ŽTreviâ€¬Fountain is collected every day & donated to â€ª#â€Žcharityâ€¬ "));
    }

    public function index2() {

    }
}