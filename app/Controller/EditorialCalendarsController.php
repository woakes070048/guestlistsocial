<?php
class EditorialCalendarsController extends AppController {
    public $components = array('Session', 'Auth', 'Pusher.Pusher');
    public $helpers =  array('Html' , 'Form', 'Session', 'Pusher.Pusher');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar', 'Editor');

    //saving editorial calendars
    public function calendarsave() {
        $data = $this->request->data;
        if ($data) {
            foreach ($data['EditorialCalendar'] as $key) {
                $id = $key['id'];
                $this->EditorialCalendar->id = $id;
                $this->EditorialCalendar->save($key);

                $tweets = $this->Tweet->find('all', array('conditions' => array('calendar_id' => $id, 'timestamp >' => time())));
                if ($tweets) {
                    foreach ($tweets as $key1) {
                        $id = $key1['Tweet']['id'];
                        $this->Tweet->id = $id;

                        $newtime = date('d-m-Y', $key1['Tweet']['timestamp']) . " " . $key['time'];
                        $newtime = date('d-m-Y H:i', strtotime($newtime)); //this line corrects formatting issues with $key['time']

                        $newtimestamp = strtotime($newtime);

                        $this->Tweet->saveField('time', $newtime);
                        $this->Tweet->saveField('timestamp', $newtimestamp);

                        if ($key1['Tweet']['verified'] == 1) {
                            $this->CronTweet->saveField('time', $newtime);
                            $this->CronTweet->saveField('timestamp', $newtimestamp);
                        }
                    }
                }
            }
        }
        $this->redirect(Controller::referer());
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
        foreach ($this->request->data['Tweet'] as $key) {
            if (!empty($key['id'])) {
                $originals[] = $key['id'];
            }
        }
        $originals = $this->Tweet->find('all', array('conditions' => array('id' => $originals)));
        foreach ($originals as $key => $value) {
            $originals[$value['Tweet']['id']] = $originals[$key];
            unset($originals[$key]);
        }
        foreach ($this->request->data['Tweet'] as $value => $key) {
            if (empty($key['body']) && empty($key['id'])) { //Empty Tweets
                unset($this->request->data['Tweet'][$value]);
            } elseif (!empty($key['id'])) { //Edited Tweets
                $original = $originals[$key['id']];
                $key['account_id'] = $this->Session->read('access_token.account_id');
                $key['time'] = $key['timestamp'];
                $key['timestamp'] = strtotime($key['timestamp']);
                if ($original['Tweet']['body'] != $key['body']) {
                    if ($original['Tweet']['verified'] == 0 && $key['verified'] == 1) {
                        $key['verified'] = 1;
                    } elseif ($original['Tweet']['verified'] == 1 && $key['verified'] == 1) {
                        $key['verified'] = 1;
                    } else {
                        $key['verified'] = 0;
                    }
                    $key['first_name'] = $this->Session->read('Auth.User.first_name');
                    $edited = true;
                } else {
                    if ($original['Tweet']['verified'] == $key['verified'] && empty($key['img_url1']['name'])) {
                        $uneditedTweet = 1;
                    }
                    $edited = false;
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

                if ($original['Tweet']['verified'] != 1 && $key['verified'] == 1) {
                    $key['verified_by'] = $this->Session->read('Auth.User.first_name');
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
                    //$key['img_url'] = $original['Tweet']['img_url'];
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
                if (!empty($key['verified_by'])) {
                    $toSave['Tweet']['verified_by'] = $key['verified_by'];
                }

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



                if ($key['body']) {
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
                }

                //if ($original['Tweet']['body'] != $key['body'] || $original['Tweet']['verified'] != $toSave['verified']) {
                if (empty($uneditedTweet)) {
                    $test[] = $toSave;
                } else {
                    unset($uneditedTweet);
                }
                //}

            } else { //New Tweets
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

                $toSave = array();
                $toSave['Tweet']['body'] = $key['body'];
                $toSave['Tweet']['verified'] = $key['verified'];
                $toSave['Tweet']['user_id'] = $key['user_id'];
                $toSave['Tweet']['verified_by'] = $key['verified_by'];
                if (!empty($key['img_url'])) {
                    $toSave['Tweet']['img_url'] = $key['img_url'];
                }
                $toSave['Tweet']['calendar_id'] = $key['calendar_id'];
                $toSave['Tweet']['timestamp'] = $key['timestamp'];
                $toSave['Tweet']['time'] = $key['time'];
                $toSave['Tweet']['account_id'] = $key['account_id'];
                $toSave['Tweet']['first_name'] = $key['first_name'];
                $toSave['Editor'] = array($key['Editor']);

                if ($key['body']) {
                    //$this->Tweet->create();
                    //$this->Tweet->save($toSave);
                    if ($toSave['Tweet']['verified'] == 1 && $key['timestamp'] > time()) {
                        //$this->CronTweet->save($toSave);
                        $verified[] = $toSave;
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
                $this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again');
            }
        }

        if (!empty($verified)) {
            if ($this->CronTweet->saveAll($verified)) {
            
            } else {
                $this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again');
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

    public function deleteCalendar($id) {
        $this->EditorialCalendar->delete($id);
        $this->redirect(Controller::referer());
    }

    public function calendarRefresh ($months) {
        $calendar = $this->EditorialCalendar->find('all', array('recursive' => -1, 'conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        
        $tweets = array();
        foreach ($calendar as $key) {
            $tweets[$key['EditorialCalendar']['id']] = $this->Tweet->find('all', array('conditions' => array('calendar_id' => $key['EditorialCalendar']['id'], 'timestamp >=' => strtotime(date('M Y') . ' + ' . ($months) . 'months'), 'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months')), 'order' => array('Tweet.timestamp' => 'ASC'), 'recursive' => 2));
        }
        $this->set('tweets', $tweets);
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

    public function recycle($calendar_id, $day) {
        //$tweet = $this->Tweet->find('first', array('conditions' => array('Tweet.id' => $tweet_id)));
        //$calendarID = $tweet['Tweet']['calendar_id'];
        $calendar = $this->EditorialCalendar->find('first', array('conditions' => array('id' => $calendar_id)));
        $tweets = $this->Tweet->find('all', array('conditions' => array('calendar_id' => $calendar_id, 'verified' => 1, 'published' => 1)));
        //debug($tweets);
        $topic = $calendar['EditorialCalendar'][strtolower($day) . '_topic'];
        $test = array();
        foreach ($tweets as $key) {
            $date = date('F Y', $key['Tweet']['timestamp']);
            if (date('l', $key['Tweet']['timestamp']) == $day) {
                $test[$date][] = $key['Tweet'];
            }
        }
        //$this->set('tweet', $tweet);
        $this->set('test', $test);
        $this->set('topic', $topic);
        $this->layout = '';
    }
}