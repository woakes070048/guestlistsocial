<?php
class EditorialCalendarsController extends AppController {
    public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form', 'Session');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar');

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
        debug($originals);
        foreach ($this->request->data['Tweet'] as $key) {
            if (!$key['body'] && !$key['id']) { //Empty Tweets

            } elseif (!empty($key['id'])) { //Edited Tweets
                $original = $originals[$key['id']];
                $key['first_name'] = $this->Session->read('Auth.User.first_name');
                $key['account_id'] = $this->Session->read('access_token.account_id');
                $key['time'] = $key['timestamp'];
                $key['timestamp'] = strtotime($key['timestamp']);

                if ($original['Tweet']['body'] != $key['body']) {
                    if ($original['Tweet']['verified'] == 0 && $key['verified'] == 1) {
                        $key['verified'] = 1;
                    } else {
                        $key['verified'] = 0;
                    }
                    $key['user_id'] = $this->Session->read('Auth.User.id');
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
                $toSave['id'] = $key['id'];
                $toSave['body'] = $key['body'];
                $toSave['verified'] = $key['verified'];
                if (!empty($key['user_id'])) {
                    $toSave['user_id'] = $key['user_id'];
                }
                if (!empty($key['verified_by'])) {
                    $toSave['verified_by'] = $key['verified_by'];
                }
                $toSave['img_url'] = $key['img_url'];
                $toSave['first_name'] = $key['first_name'];

                if ($key['body']) {
                    //$this->Tweet->save($toSave);
                    if ($toSave['verified'] == 1 && $key['timestamp'] > time()) {
                        //$this->CronTweet->save($key);
                        $verified[] = $key;
                    } elseif ($toSave['verified'] != 1 && $original['Tweet']['verified'] == 1) {
                        $this->CronTweet->delete($key['id']);
                    }
                } else {
                    $this->Tweet->delete($key['id']);
                    $this->CronTweet->delete($key['id']);
                }
                $test[] = $toSave;

            } else { //New Tweets
                $key['first_name'] = $this->Session->read('Auth.User.first_name');

                $key['time'] = $key['timestamp'];
                $key['timestamp'] = strtotime($key['timestamp']);
                $key['user_id'] = $this->Session->read('Auth.User.id');
                $key['account_id'] = $this->Session->read('access_token.account_id');

                if (empty($key['verified'])) {
                    $key['verified'] = 0;
                }


                if (empty($key['verified_by'])) {
                    $key['verified_by'] = '';
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
                $toSave['body'] = $key['body'];
                $toSave['verified'] = $key['verified'];
                $toSave['user_id'] = $key['user_id'];
                $toSave['verified_by'] = $key['verified_by'];
                if (!empty($key['img_url'])) {
                    $toSave['img_url'] = $key['img_url'];
                }
                $toSave['calendar_id'] = $key['calendar_id'];
                $toSave['timestamp'] = $key['timestamp'];
                $toSave['time'] = $key['time'];
                $toSave['account_id'] = $key['account_id'];
                $toSave['first_name'] = $key['first_name'];

                if ($key['body']) {
                    //$this->Tweet->create();
                    //$this->Tweet->save($toSave);
                    if ($toSave['verified'] == 1 && $key['timestamp'] > time()) {
                        //$this->CronTweet->save($toSave);
                        $verified[] = $toSave;
                        unset($toSave);
                    }
                }
                $test[] = $toSave;
            }
            unset($key);
        }
        if (!empty($verified)) {
            $this->Tweet->saveAll($test);
        }

        if (!empty($verified)) {
            $this->CronTweet->saveAll($verified);
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
        $calendar = $this->EditorialCalendar->find('all', array('conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC')));
        
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
}