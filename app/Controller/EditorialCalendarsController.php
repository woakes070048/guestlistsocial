<?php
class EditorialCalendarsController extends AppController {
    public $components = array('Session', 'Auth', 'Pusher.Pusher');
    public $helpers =  array('Html' , 'Form', 'Session', 'Pusher.Pusher');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar', 'Editor', 'TeamsUser', 'BankCategory', 'TweetBank');

    //saving editorial calendars
    public function calendarsave() {
        debug($this->request->data);
        $data = $this->request->data;
        $saveTweets = array();
        $saveCronTweets = array();
        if (!empty($data)) {
            $saveCalendars = array();
            foreach ($data['EditorialCalendar'] as $id => $key) {
                if (!empty($key['changed'])) {
                    $x = array();
                    if (!empty($key['bank_category_manual'])) {
                        $bc = array();
                        unset($key['bank_category_id']);
                        $bc['BankCategory']['category'] = $key['bank_category_manual'];
                        $bc['BankCategory']['account_id'] = $this->Session->read('access_token.account_id');
                        $this->BankCategory->save($bc);
                        $key['bank_category_id'] = $this->BankCategory->getLastInsertID();
                    }
                    if (!empty($key['bank_category_id'])) {
                        $x['EditorialCalendar']['bank_category_id'] = $key['bank_category_id'];
                    }
                    $x['EditorialCalendar']['time'] = $key['time'];
                    $x['EditorialCalendar']['twitter_account_id'] = $this->Session->read('access_token.account_id');
                    if (!empty($key['day'])) {
                        $x['EditorialCalendar']['day'] = $key['day'];
                    }

                    if (is_int($id)) {
                        $x['EditorialCalendar']['id'] = $id;
                        $tweets = $this->Tweet->find('all', array('conditions' => array('calendar_id' => $id, 'timestamp >' => time())));
                        if (!empty($tweets)) {
                            foreach ($tweets as $key1) {
                                $x1 = array();
                                $id = $key1['Tweet']['id'];

                                $newtime = date('d-m-Y', $key1['Tweet']['timestamp']) . " " . $key['time'];
                                $newtime = date('d-m-Y H:i', strtotime($newtime)); //this line corrects formatting issues with $key['time']

                                $newtimestamp = strtotime($newtime);

                                $x1['Tweet']['id'] = $id;
                                $x1['Tweet']['time'] = $newtime;
                                $x1['Tweet']['timestamp'] = $newtimestamp;

                                $saveTweets[] = $x1;
                                if ($key1['Tweet']['verified'] == 1) {
                                    $saveCronTweets[] = $x1['Tweet'];
                                }
                            }
                            unset($key1);
                        }
                    }

                    $saveCalendars[] = $x;  
                }
            }
            debug($saveCalendars);
            if (!empty($saveTweets)) {
                $this->Tweet->saveAll($saveTweets);
            }
            if (!empty($saveCronTweets)) {
                $this->CronTweet->saveAll($saveCronTweets);
            }
            if ($this->EditorialCalendar->saveAll($saveCalendars)) {
                $this->response->statusCode(200);
            } else {
                $this->response->statusCode(500);
            }
        }
        return $this->response;
        //$this->redirect(Controller::referer());
    }

    //old save process that had a lot of errors
    /*public function editcalendartweet() {
        foreach ($this->request->data['Tweet'] as $key) {
            if (!empty($key['id']) || !empty($key['body'])) {
                if ($key['id']) {
                    $id = $key['id'];
                    $this->Tweet->id = $id;
                    $this->CronTweet->id = $id;
                    $tweet = $this->Tweet->find('first', array('conditions' => array('id' => $id)));
                } else {
                    $key['first_name'] = $this->Session->read('Auth.User.first_name');
                }
    
                if ($key['timestamp']) {
                    $key['time'] = $key['timestamp'];
                    $key['timestamp'] = strtotime($key['timestamp']);
                } else {
                    $key['timestamp'] = 0;
                }
    
                    //if (!isset($key['verified'])) {
                    //    $key['verified'] = 0;
                    //}
    
    
                            //debug($tweet);
                            //debug($key);
                    //if tweet is set to 'needs improving' and body has been changed, set back to 'awaiting proof'
                    if (!empty($tweet)) {
                        if ($tweet['Tweet']['verified'] == 2 && $key['verified'] == 2) {
                            if ($tweet['Tweet']['body'] != $key['body']) {
                                $key['verified'] = 0;
                            }
                        }
                    }
    
                    //If it's a new tweet OR if you've altered the tweet body
                    if (empty($tweet) || $tweet['Tweet']['body'] != $key['body']) {
                        $key['user_id'] = $this->Session->read('Auth.User.id');
                        $key['verified'] = 0;
                    }

                    if (empty($key['verified'])) {
                        $key['verified'] = 0;
                    }
    
                    $key['account_id'] = $this->Session->read('access_token.account_id');
    
                    //Handling images
                if ($key['img_url1']['error'] == 0) {
                    $z = explode(".", $key['img_url1']['name']);
                    $extension = end($z);
                    $allowed_extensions = array("gif", "jpeg", "jpg", "png");
    
                    if (in_array($extension, $allowed_extensions)) {
                        $newFileName = $this->Session->read('Auth.User.id') . md5(mt_rand(1000000000,9999999999)) . "." . $extension;
                        move_uploaded_file($key['img_url1']['tmp_name'], '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName);
                        $key['img_url'] = "http://social.guestlist.net/img/uploads/".$newFileName;
                    } else {
                        $this->Session->setFlash('You can only upload images.');
                    }

                } elseif ($key['img_url1']['error'] == 1) {
                    $this->Session->setFlash('Image too large, please try another image (Max 2MB)');
                }
                
                if ($key['body']) {
                    if ($this->Tweet->save($key)) {
                        if ($key['verified'] == 1) {
                            $this->CronTweet->save($key);
                            $this->CronTweet->deleteAll(array('timestamp' => 0));
                        }
                    } else {
                    $this->Session->setFlash('Unable to update your post.');
                    }
                } elseif ($key['id'] && !$key['body']) {
                    $this->Tweet->delete($key['id']);
                    $this->CronTweet->delete($key['id']);
                }

            }
            unset($key);
        }

        $this->redirect(Controller::referer());
    }*/

    public function editcalendartweet1() {
        $test = array();
        $verified = array();
        $originals = array();
        $calendarIDs = array();
        foreach ($this->request->data['Tweet'] as $key) {
            if (!empty($key['id'])) {
                $originals[] = $key['id'];
            }
            $calendarIDs[] = $key['calendar_id'];
        }
        $originals = $this->Tweet->find('all', array('conditions' => array('Tweet.id' => $originals)));
        $calendars = $this->EditorialCalendar->find('all', array('conditions' => array('EditorialCalendar.id' => $calendarIDs)));
        $calendars = Hash::combine($calendars, '{n}.EditorialCalendar.id', '{n}');
        foreach ($originals as $key => $value) {
            $originals[$value['Tweet']['id']] = $originals[$key];
            unset($originals[$key]);
        }
        foreach ($this->request->data['Tweet'] as $value => $key) {
            if (empty($key['body']) && empty($key['id'])) { //Empty Tweets
                unset($this->request->data['Tweet'][$value]);
            } elseif (!empty($key['id'])) { //Edited Tweets
                if (!empty($key['body'])) {
                    $key['body'] = trim($key['body']);
                }
                $original = $originals[$key['id']];
                $key['account_id'] = $this->Session->read('access_token.account_id');
                $key['time'] = $key['timestamp'];
                $key['timestamp'] = strtotime($key['timestamp']);
                if ($original['Tweet']['body'] != $key['body']) {
                    if ($original['Tweet']['verified'] == 0 && $key['verified'] == 1) {
                        $key['verified'] = 1;
                    } elseif ($original['Tweet']['verified'] == 1 && $key['verified'] == 1 && $key['forceVerified'] == 'true') {
                        $key['verified'] = 1;
                    } else {
                        $key['verified'] = 0;
                    }
                    $key['first_name'] = $this->Session->read('Auth.User.first_name');
                    $edited = true;
                    $newTweetBank = true;
                } else {
                    $edited = false;
                    if ($original['Tweet']['verified'] == $key['verified'] && empty($key['img_url1']['name'])) {
                        $uneditedTweet = 1;
                    } elseif ($original['Tweet']['verified'] == $key['verified'] && !empty($key['img_url1']['name'])) {//only changed image
                        $key['verified'] = 0;
                        $edited = true;
                    }
                }

                if ($key['verified'] == 2) {
                    $improve = true;
                } else {
                    $improve = false;
                }

                if ($original['Tweet']['verified'] != $key['verified'] && $key['verified'] == 1) {
                    $proofed = true;
                } else {
                    $proofed = false;
                }

                if (!isset($key['verified'])) {
                    $key['verified'] = $original['Tweet']['verified'];
                }

                if (empty($key['img_url'])) {
                    unset($key['img_url']);
                }

                //Image Handling
                if (!empty($key['img_url1']['name'])) {
                    if ($x = $this->imageHandling($key)) {
                        $key['img_url'] = $x;
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
                    $edited = true;
                } else {
                    $key['img_url'] = $original['Tweet']['img_url'];
                }

                if (!empty($key['img_url'])) {
                    if (strlen($key['body']) > 117 && $key['verified'] == 1) {
                        $key['verified'] = 0;
                    }
                } else {
                    if (strlen($key['body']) > 140 && $key['verified'] == 1) {
                        $key['verified'] = 0;
                    }
                }

                $toSave = array();
                $toSave['Tweet']['id'] = $key['id'];
                $toSave['Tweet']['body'] = $key['body'];
                $toSave['Tweet']['verified'] = $key['verified'];
                $toSave['Tweet']['account_id'] = $key['account_id'];
                $toSave['Tweet']['timestamp'] = $key['timestamp'];
                $toSave['Tweet']['time'] = $key['time'];

                if (!empty($key['img_url'])) {
                    $toSave['Tweet']['img_url'] = $key['img_url'];
                }
                
                if (!empty($key['first_name'])) {
                    $toSave['Tweet']['first_name'] = $key['first_name'];
                }

                foreach ($original['Editor'] as $editor) {
                    if ($editor['type'] == 'written') {
                        $written_by = $editor['user_id'];
                    }
                }
                
                if ($improve) {
                    $toSave['Editor'][] = array('type' => 'improve', 'user_id' => $this->Session->read('Auth.User.id'));
                }

                if ($edited) {
                    if ($written_by != $this->Session->read('Auth.User.id')) {
                        $toSave['Editor'][] = array('type' => 'edited', 'user_id' => $this->Session->read('Auth.User.id'));
                    }
                } 

                if ($proofed) {
                    $toSave['Editor'][] = array('type' => 'proofed', 'user_id' => $this->Session->read('Auth.User.id'));
                }
                
                if (!empty($newTweetBank)) {
                    if (!empty($calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'])) {
                        $toSave['TweetBank']['bank_category_id'] = $calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'];
                        $toSave['TweetBank']['body'] = $key['body'];
                        if (!empty($key['img_url'])) {
                            $toSave['TweetBank']['img_url'] = $key['img_url'];
                        }
                    }
                }



                if (!empty($key['body'])) {
                    //$this->Tweet->save($toSave);
                    if ($toSave['Tweet']['verified'] == 1 && $key['timestamp'] > time()) {
                        //$this->CronTweet->save($key);
                        $verified[] = $toSave['Tweet'];
                    } elseif ($toSave['Tweet']['verified'] != 1 && $original['Tweet']['verified'] == 1) {
                        $this->CronTweet->delete($key['id']);
                    }
                } else {
                    $this->Tweet->delete($key['id']);
                    $this->CronTweet->delete($key['id']);
                    $uneditedTweet = 1;
                }
                //if ($original['Tweet']['body'] != $key['body'] || $original['Tweet']['verified'] != $toSave['verified']) {
                if (empty($uneditedTweet)) {
                    $test[] = $toSave;
                } else {
                    unset($uneditedTweet);
                }
                //}

            } else { //New Tweets
                $key['body'] = trim($key['body']);
                $key['first_name'] = $this->Session->read('Auth.User.first_name');

                $key['time'] = $key['timestamp'];
                $key['timestamp'] = strtotime($key['timestamp']);
                $key['user_id'] = $this->Session->read('Auth.User.id');
                $key['account_id'] = $this->Session->read('access_token.account_id');

                $key['Editor']['user_id'] = $this->Session->read('Auth.User.id');
                $key['Editor']['type'] = 'written';

                if (empty($key['verified'])) {
                    $key['verified'] = 0;
                }


                if (empty($key['verified_by'])) {
                    $key['verified_by'] = '';
                }


                if ($key['verified'] == 1) {
                    $key['verified_by'] = $this->Session->read('Auth.User.first_name');
                }

                if (empty($key['img_url'])) {
                    unset($key['img_url']);
                }

                //Image Handling
                if (!empty($key['img_url1']['name'])) {
                    if ($x = $this->imageHandling($key)) {
                        $key['img_url'] = $x;
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
                }

                if (!empty($calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'])) {
                    $key['TweetBank']['bank_category_id'] = $calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'];
                    $key['TweetBank']['body'] = $key['body'];
                    if (!empty($key['img_url'])) {
                        $key['TweetBank']['img_url'] = $key['img_url'];
                    }
                }

                $toSave = array();
                $toSave['Tweet']['body'] = $key['body'];
                $toSave['Tweet']['verified'] = $key['verified'];
                $toSave['Tweet']['user_id'] = $key['user_id'];
                if (!empty($key['img_url'])) {
                    $toSave['Tweet']['img_url'] = $key['img_url'];
                }
                $toSave['Tweet']['calendar_id'] = $key['calendar_id'];
                $toSave['Tweet']['timestamp'] = $key['timestamp'];
                $toSave['Tweet']['time'] = $key['time'];
                $toSave['Tweet']['account_id'] = $key['account_id'];
                $toSave['Tweet']['first_name'] = $key['first_name'];
                $toSave['Editor'] = array($key['Editor']);
                $toSave['TweetBank'] = $key['TweetBank'];

                if ($key['body']) {
                    //$this->Tweet->create();
                    //$this->Tweet->save($toSave);
                    if ($toSave['Tweet']['verified'] == 1 && $key['timestamp'] > time()) {
                        //$this->CronTweet->save($toSave);
                        $verified[] = $toSave['Tweet'];
                        //unset($toSave);
                    }
                }
                $test[] = $toSave;
            }
            unset($key);
        }

        if (!empty($test)) {
            if ($this->Tweet->saveAll($test, array('deep' => true))) {

            } else {
                $errors = $this->Tweet->invalidFields();
                if (!empty($errors)) {
                    foreach ($errors as $key => $value) {
                        if (!empty($value['TweetBank']['body'])) {
                            unset($test[$key]['TweetBank']);
                        }

                        if ($this->Tweet->saveAll($test, array('deep' => true))) {
                        } else {
                            $this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again');
                        }
                    }
                }
            }
        }

        if (!empty($verified)) {
            if ($this->CronTweet->saveAll($verified)) {
            
            } else {
                $this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again1');
            }
        }
        $this->redirect(Controller::referer());
    }

    private function imageHandling($key) {
        debug($key['img_url1']['name']);
        if ($key['img_url1']['error'] == 0) {
            $z = explode(".", $key['img_url1']['name']);
            $extension = end($z);
            $allowed_extensions = array("gif", "jpeg", "jpg", "png");
        
            if (in_array(strtolower($extension), $allowed_extensions)) {
                $newFileName = $this->Session->read('Auth.User.id') . "-" . $key['account_id'] . "-" . $key['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
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

    public function addCalendar() {
        $this->EditorialCalendar->create();
        $this->EditorialCalendar->saveField('twitter_account_id', $this->Session->read('access_token.account_id'));
        $this->EditorialCalendar->saveField('team_id', $this->Session->read('Auth.User.Team.id'));
        $this->redirect(Controller::referer());
    }

    public function deleteCalendar($account_id, $time) {
        $teamIDs = $this->TeamsUser->find('list', array('fields' => 'team_id', 'conditions' => array('user_id' => $this->Session->read('Auth.User.id'), 'group_id' => 1)));
        $time = str_split($time, 2);
        $time = $time[0] . ":" . $time[1];
        $ids = array();
        if ($this->TwitterPermission->hasAny(array('team_id' => $teamIDs, 'twitter_account_id' => $account_id))) {
            $calendars = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $account_id, 'time' => $time)));
            foreach ($calendars as $key) {
                $ids[] = $key['EditorialCalendar']['id'];
            }
            if ($this->EditorialCalendar->delete($ids)) {
                $this->response->statusCode(200);
            } else {
                $this->response->statusCode(500);
            }
        }
        //debug($ids);
        //$this->EditorialCalendar->delete($id);
        return $this->response;
        $this->redirect(Controller::referer());
    }

    public function calendarRefresh ($months) {
        $calendar = $this->EditorialCalendar->find('all', array('recursive' => 1, 'conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC'), 'contain' => array('TwitterAccount', 'BankCategory', 'Tweet' => array('conditions' => array('timestamp >=' => strtotime(date('M Y') . ' + ' . ($months) . 'months'), 'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months')), 'order' => array('Tweet.timestamp' => 'ASC'), 'Comment', 'Editor' => array('User')))));
        $calendarx = array();
        $c = array();
        foreach ($calendar as $key) {
            $calendarx[$key['EditorialCalendar']['time']][$key['EditorialCalendar']['day']] = $key;
            $c[] = $key['EditorialCalendar']['id'];
        }
        $calendar = $calendarx;
        
        $tweets = array();
        //foreach ($calendar as $key) {
        //    $tweets[$key['EditorialCalendar']['id']] = $this->Tweet->find('all', array('conditions' => array('calendar_id' => $key['EditorialCalendar']['id'], 'timestamp >=' => strtotime(date('M Y') . ' + ' . ($months) . 'months'), 'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months')), 'order' => array('Tweet.timestamp' => 'ASC'), 'recursive' => 2));
        //}
        //$tweets = $this->Tweet->find('all', array('conditions' => array('calendar_id' => $c, 'timestamp >=' => strtotime(date('M Y') . ' + ' . ($months) . 'months'), 'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months')), 'order' => array('Tweet.timestamp' => 'ASC'), 'recursive' => 2));
        /*foreach ($tweets as $key) {
            $tweetsx[$key['EditorialCalendar']['id']][] = $key;

        }
        $tweets = $tweetsx;
        $this->set('tweets', $tweets);*/
        $this->set('calendar', $calendar);
        
        if (isset($months)) {
            $this->set('months', $months);
        }
        $this->layout = '';
    }
    
    public function editorialRefresh() {
        $calendar = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        $this->set('calendar', $calendar);
        $this->layout = '';
    }

    public function hideCalendar() {
        $this->Session->write('Auth.User.show_calendar', 0);
    }

    public function showCalendar() {
        $this->Session->write('Auth.User.show_calendar', 1);
    }

    public function recycle($calendar_id) {
        $teamIDs = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $teamIDs[] = $key['id'];
        }
        $accounts = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $teamIDs)));
        $screen_names = $this->TwitterAccount->find('list', array('fields' => 'screen_name', 'conditions' => array('account_id' => $accounts), 'order' => array('screen_name' => 'ASC')));
        $this->set('accounts', $screen_names);
        
        if (isset($this->request->data['accountSubmit'])) {
            $screen_name = $this->request->data['accountSubmit'];
            $new_oauth_tokens = $this->TwitterAccount->find('first', array('conditions' => array('screen_name' => $screen_name)));
            $account_id = $new_oauth_tokens['TwitterAccount']['account_id'];
            $this->set('selected', $this->request->data['accountSubmit']);
        } else {
            $account_id = $this->Session->read('access_token.account_id');
            $this->set('selected', $this->Session->read('access_token.screen_name'));
        }

        $categories = $this->BankCategory->find('all', array('conditions' => array('account_id' => $account_id)));
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
            $tweetBanks = $this->TweetBank->find('all', array('conditions' => array('bank_category_id' => $this->request->data['BankCategory'])));

        } else {
            $calendar = $this->EditorialCalendar->find('first', array('conditions' => array('EditorialCalendar.id' => $calendar_id)));
            $bank_category_id = $calendar['BankCategory']['id'];
            $this->set('selectedCategories', $bank_category_id);
            $tweetBanks = $this->TweetBank->find('all', array('conditions' => array('bank_category_id' => $bank_category_id)));
        }

        $this->set('tweetBanks', $tweetBanks);
        $this->layout = '';
    }
}