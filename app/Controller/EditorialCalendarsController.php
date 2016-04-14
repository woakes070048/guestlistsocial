<?php

class EditorialCalendarsController extends AppController {
    public $components = array('Session', 'Auth', 'Pusher.Pusher', 'Cookie');
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
                        $colors = array('#337ab7', '#5bc0de', '#5cb85c', '#d9534f', '#f0ad4e', '#8465C1');
                        $bc['BankCategory']['color'] = $colors[array_rand($colors)];
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


            if ($this->Session->read('Auth.User.first_login_complete') == 2) {
                $this->User->id = $this->Session->read('Auth.User.id');
                $this->User->saveField('first_login_complete', 3);
                $this->refreshUser();
            }
        }
        return $this->response;
        $this->redirect(Controller::referer());
    }


    public function editcalendartweet() {
        $id = key($this->request->data['Tweet']);
        $data['Tweet'] = $this->request->data['Tweet'][$id];
        if (ctype_digit($id)) {
            $original = $this->Tweet->find('first', array('conditions' => array('Tweet.id' => $id)));   
        }
        $calendar = $this->EditorialCalendar->find('first', array('conditions' => array('EditorialCalendar.id' => $data['Tweet']['calendar_id']), 'contain' => 'BankCategory'));

        if (empty($data['Tweet']['body']) && empty($data['Tweet']['id'])) { //Empty Tweets

            unset($this->request->data);

        } elseif (ctype_digit($data['Tweet']['id']) && !empty($data['Tweet']['body'])) { //Edited Tweets

            $save['Tweet']['id'] = $id;
            $save['Tweet']['body'] = trim($data['Tweet']['body']);
            $save['Tweet']['account_id'] = $this->Session->read('access_token.account_id');
            $save['Tweet']['time'] = $data['Tweet']['time'];
            $save['Tweet']['timestamp'] = strtotime($data['Tweet']['time']);
            if ($original['Tweet']['body'] != $data['Tweet']['body']) {//Has the body been changed? If body is changed, tweet becomes unverified
                if ($original['Tweet']['verified'] == 0 && $data['Tweet']['verified'] == 1) {//force verified
                    $save['Tweet']['verified'] = 1;
                } elseif ($data['Tweet']['verified'] == 1 && $data['Tweet']['forceVerified'] == 'true') {//force verified
                    $save['Tweet']['verified'] = 1;
                } else {
                    $save['Tweet']['verified'] = 0;
                }
                $save['Tweet']['first_name'] = $this->Session->read('Auth.User.first_name');
                $edited = true;
            } else {
                $edited = false;
                if ($original['Tweet']['verified'] == $data['Tweet']['verified'] && empty($data['Tweet']['img_url1']['name']) && empty($data['Tweet']['img_url2'])) {

                    $edited = false;

                } elseif ($original['Tweet']['verified'] == $data['Tweet']['verified'] && (!empty($data['Tweet']['img_url1']['name']) || !empty($data['Tweet']['img_url2']))) {//only changed image

                    $save['Tweet']['verified'] = 0;
                    $edited = true;

                } elseif ($original['Tweet']['verified'] != $data['Tweet']['verified']) {
                    $save['Tweet']['verified'] = $data['Tweet']['verified'];
                    $edited = true;
                }
            }

            if ($data['Tweet']['verified'] == 2) {
                $improve = true;
            } else {
                $improve = false;
            }


            if ($original['Tweet']['verified'] != $data['Tweet']['verified'] && $data['Tweet']['verified'] == 1) {
                $proofed = true;
            } else {
                $proofed = false;
            }


            if (!isset($save['Tweet']['verified'])) {
                $save['Tweet']['verified'] = $original['Tweet']['verified'];
            } else {
                if ($save['Tweet']['verified'] == 1 && !empty($data['Tweet']['tweet_bank_id']) && $edited == false) {//tweet bank id present, not edited
                    $save['Tweet']['tweet_bank_id'] = $original['Tweet']['tweet_bank_id'];
                } elseif ($save['Tweet']['verified'] == 1 && !empty($data['Tweet']['tweet_bank_id']) && $edited == true) {//tweet bank id present, edited
                    $save['Tweet']['tweet_bank_id'] = $data['Tweet']['tweet_bank_id'];
                } elseif ($save['Tweet']['verified'] == 1 && empty($data['Tweet']['tweet_bank_id'])) {//new tweet with no tweet bank id
                    $newTweetBank = true;
                } elseif ($save['Tweet']['verified'] == 1 && $edited == true) {//edited tweet with no tweet bank id
                    $newTweetBank = true;
                }
            }

            //Image Handling
            if (!empty($data['Tweet']['img_url1']['name']) && empty($save['Tweet']['img_url'])) {
                if ($x = $this->imageHandling($data['Tweet'])) {
                    $save['Tweet']['img_url'] = $x;
                    $edited = true;
                    if ($save['Tweet']['verified'] == 1) {
                        $newTweetBank = true;  
                    }
                } else {
                    //$this->Session->setFlash('There was an error processing your image, please try again.');
                }
            } elseif (!empty($data['Tweet']['img_url2'])) {
                $z = explode(".", $data['Tweet']['img_url2']);
                $extension = end($z);
                $allowed_extensions = array("gif", "jpeg", "jpg", "png");


                $arrContextOptions=array(
                    "ssl"=>array(
                        //"cafile"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.ca-bundle",
                        //"local_cert"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.pem",
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                ); 
            
                if (in_array(strtolower($extension), $allowed_extensions)) {
                    $newFileName = $this->Session->read('Auth.User.id') . "-" . $data['Tweet']['account_id'] . "-" . $data['Tweet']['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                    $save['Tweet']['img_url'] = '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName;
                    if (copy($data['Tweet']['img_url2'], $save['Tweet']['img_url'], stream_context_create($arrContextOptions))) {
                        $save['Tweet']['img_url'] = 'https://tweetproof.com/img/uploads/' .$newFileName;
                        $edited = true;
                        if ($save['Tweet']['verified'] == 1) {
                            $newTweetBank = true;  
                        }
                    } else {
                        unset($save['Tweet']['img_url']);
                        $this->Session->setFlash('Image failed to upload. Please try again');
                    }
                } else {
                    $this->Session->setFlash('You can only use images');
                }
            } else {
                if (empty($data['Tweet']['img_url'])) {
                    $save['Tweet']['img_url'] = $original['Tweet']['img_url'];
                } else {
                    $save['Tweet']['img_url'] = $data['Tweet']['img_url'];
                }
            }

            //error if tweet is over 140 chars
            if (!empty($save['Tweet']['img_url'])) {
                if (strlen($save['Tweet']['body']) > 117 && $save['Tweet']['verified'] == 1) {
                    $save['Tweet']['verified'] = 0;
                }
            } else {
                if (strlen($save['Tweet']['body']) > 140 && $save['Tweet']['verified'] == 1) {
                    $save['Tweet']['verified'] = 0;
                }
            }

            if (isset($newTweetBank)) {
                if (!empty($calendar['EditorialCalendar']['bank_category_id'])) {
                    $save['TweetBank']['bank_category_id'] = $calendar['EditorialCalendar']['bank_category_id'];
                    $save['TweetBank']['body'] = $save['Tweet']['body'];
                    if (!empty($save['Tweet']['img_url'])) {
                        $save['TweetBank']['img_url'] = $save['Tweet']['img_url'];
                    }
                }
            }

            foreach ($original['Editor'] as $editor) {
                if ($editor['type'] == 'written') {
                    $written_by = $editor['user_id'];
                }
            }
            

            if ($improve) {
                $save['Editor'][] = array('type' => 'improve', 'user_id' => $this->Session->read('Auth.User.id'));
            } else {
                if ($edited) {
                    if (!empty($written_by)) {
                        if ($written_by != $this->Session->read('Auth.User.id')) {
                            $save['Editor'][] = array('type' => 'edited', 'user_id' => $this->Session->read('Auth.User.id'));
                        }
                    } else {
                        $save['Editor'][] = array('type' => 'edited', 'user_id' => $this->Session->read('Auth.User.id'));
                    }
                } 
            }

            if ($proofed) {
                $save['Editor'][] = array('type' => 'proofed', 'user_id' => $this->Session->read('Auth.User.id'));
            }


            if (!empty($save['Tweet']['body'])) {
                //$this->Tweet->save($toSave);
                if ($save['Tweet']['verified'] == 1 && $save['Tweet']['timestamp'] > time()) {
                    //$this->CronTweet->save($key);
                    $CronTweet[] = $save['Tweet'];
                } elseif ($save['Tweet']['verified'] != 1 && $original['Tweet']['verified'] == 1) {
                    $this->CronTweet->delete($data['Tweet']['id']);
                }
            }

            if (!$edited) {
                unset($save);
            }

        } elseif (!ctype_digit($data['Tweet']['id']) && !empty($data['Tweet']['body'])) { //New Tweets
            $save['Tweet']['body'] = trim($data['Tweet']['body']);
            $save['Tweet']['first_name'] = $this->Session->read('Auth.User.first_name');

            $save['Tweet']['calendar_id'] = $data['Tweet']['calendar_id'];
            $save['Tweet']['time'] = $data['Tweet']['time'];
            $save['Tweet']['timestamp'] = strtotime($data['Tweet']['time']);
            $save['Tweet']['user_id'] = $this->Session->read('Auth.User.id');
            $save['Tweet']['account_id'] = $this->Session->read('access_token.account_id');


            $save['Editor'][] = array(
                "user_id" => $this->Session->read('Auth.User.id'),
                "type" => "written"
                );


            if (empty($data['Tweet']['verified'])) {
                $save['Tweet']['verified'] = 0;
            }


            if (empty($data['Tweet']['img_url'])) {
                unset($save['Tweet']['img_url']);
            }

            //Image Handling

            if (!empty($data['Tweet']['img_url1']['name']) && empty($data['Tweet']['img_url'])) {
                if ($x = $this->imageHandling($data['Tweet'])) {
                    $save['Tweet']['img_url'] = $x;
                } else {
                    //$this->Session->setFlash('There was an error processing your image, please try again.');
                }
            } elseif (!empty($data['Tweet']['img_url2'])) {
                $z = explode(".", $data['Tweet']['img_url2']);
                $extension = end($z);
                $allowed_extensions = array("gif", "jpeg", "jpg", "png");debug($extension);


                $arrContextOptions=array(
                    "ssl"=>array(
                        //"cafile"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.ca-bundle",
                        //"local_cert"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.pem",
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                ); 
            
                if (in_array(strtolower($extension), $allowed_extensions)) {
                    $newFileName = $this->Session->read('Auth.User.id') . "-" . $save['Tweet']['account_id'] . "-" . $save['Tweet']['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                    $save['Tweet']['img_url'] = '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName;
                    if (copy($data['Tweet']['img_url2'], $save['Tweet']['img_url'])) {
                        $save['Tweet']['img_url'] = 'https://tweetproof.com/img/uploads/' .$newFileName;
                    } else {
                        unset($save['Tweet']['img_url']);
                        $this->Session->setFlash('Image failed to upload. Please try again');
                    }
                } else {
                    $this->Session->setFlash('You can only use images');
                }
            } else {
                if (!empty($data['Tweet']['img_url'])) {
                    $save['Tweet']['img_url'] = $data['Tweet']['img_url'];
                }
            }

            if (empty($data['Tweet']['tweet_bank_id']) && $data['Tweet']['verified'] == 1) {
                if (!empty($calendar['EditorialCalendar']['bank_category_id'])) {
                    $save['TweetBank']['bank_category_id'] = $calendar['EditorialCalendar']['bank_category_id'];
                    $save['TweetBank']['body'] = $save['Tweet']['body'];
                    if (!empty($save['Tweet']['img_url'])) {
                        $save['TweetBank']['img_url'] = $save['Tweet']['img_url'];
                    }
                }
                unset($save['Tweet']['tweet_bank_id']);
            } elseif (!empty($data['Tweet']['tweet_bank_id']) && $data['Tweet']['verified'] != 1) {
                $save['Tweet']['tweet_bank_id'] = $data['Tweet']['tweet_bank_id'];
            } else {
                $save['Tweet']['tweet_bank_id'] = 0;
            }

            if ($save['Tweet']['body']) {
                if ($save['Tweet']['verified'] == 1 && $save['Tweet']['timestamp'] > time()) {
                    $CronTweet = $save['Tweet'];
                }
            }

        } else {
            $this->Tweet->delete($data['Tweet']['id']);
            $this->CronTweet->delete($data['Tweet']['id']);
            $edited = false;
        }


        if (!empty($save)) {
            if ($this->Tweet->saveAll($save, array('deep' => true))) {

                $this->response->statusCode(200);

            } else {
                $errors = $this->Tweet->invalidFields();
                if (!empty($errors)) {
                    $this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again');
                    $this->response->statusCode(500);
                    $this->response->body(json_encode($errors, JSON_PRETTY_PRINT));
                }
            }
        }


        if (!empty($CronTweet)) {
            if ($this->CronTweet->saveAll($CronTweet)) {
            
                $this->response->statusCode(200);

            } else {
                //$this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again1');
                $this->response->statusCode(500);
            }
        }

        //$this->response->body(json_encode($save, JSON_PRETTY_PRINT));
        return $this->response;
    }


    public function editMultipleCalendarTweet() {
        $allIds = array();
        $originalIDs = array();
        $calendarIDs = array();
        $saveAll = array();
        $CronTweet = array();
        foreach ($this->request->data['Tweet'] as $key => $value) {
            $allIds[] = $key;
            if (ctype_digit($key)) {
                $originalIDs[] = $key;
                $calendarIDs[] = $value['calendar_id'];
            }
        }
        //$id = key($this->request->data['Tweet']);
        //$data['Tweet'] = $this->request->data['Tweet'][$id];
        $originals = $this->Tweet->find('all', array('conditions' => array('Tweet.id' => $originalIDs)));
        $originals = Hash::combine($originals, '{n}.Tweet.id', '{n}');
        $calendars = $this->EditorialCalendar->find('all', array('conditions' => array('EditorialCalendar.id' => $calendarIDs), 'contain' => 'BankCategory'));
        $calendars = Hash::combine($calendars, '{n}.EditorialCalendar.id', '{n}');
        foreach ($this->request->data['Tweet'] as $id => $value1) {
            $data['Tweet'] = $value1;
            if (ctype_digit($id)) {
                $original = $originals[$id];
                $calendar = $calendars[$data['Tweet']['calendar_id']];
            }

            if (empty($data['Tweet']['body']) && empty($data['Tweet']['id'])) { //Empty Tweets

                unset($this->request->data);

            } elseif (ctype_digit($data['Tweet']['id']) && !empty($data['Tweet']['body'])) { //Edited Tweets

                $save['Tweet']['id'] = $id;
                $save['Tweet']['body'] = trim($data['Tweet']['body']);
                $save['Tweet']['account_id'] = $this->Session->read('access_token.account_id');
                $save['Tweet']['time'] = $data['Tweet']['time'];
                $save['Tweet']['timestamp'] = strtotime($data['Tweet']['time']);
                if ($original['Tweet']['body'] != $data['Tweet']['body']) {//Has the body been changed? If body is changed, tweet becomes unverified
                    if ($original['Tweet']['verified'] == 0 && $data['Tweet']['verified'] == 1) {//force verified
                        $save['Tweet']['verified'] = 1;
                    } elseif ($data['Tweet']['verified'] == 1 && $data['Tweet']['forceVerified'] == 'true') {//force verified
                        $save['Tweet']['verified'] = 1;
                    } else {
                        $save['Tweet']['verified'] = 0;
                    }
                    $save['Tweet']['first_name'] = $this->Session->read('Auth.User.first_name');
                    $edited = true;
                } else {
                    $edited = false;
                    if ($original['Tweet']['verified'] == $data['Tweet']['verified'] && empty($data['Tweet']['img_url1']['name']) && empty($data['Tweet']['img_url2'])) {

                        $edited = false;

                    } elseif ($original['Tweet']['verified'] == $data['Tweet']['verified'] && (!empty($data['Tweet']['img_url1']['name']) || !empty($data['Tweet']['img_url2']))) {//only changed image

                        $save['Tweet']['verified'] = 0;
                        $edited = true;

                    } elseif ($original['Tweet']['verified'] != $data['Tweet']['verified']) {
                        $save['Tweet']['verified'] = $data['Tweet']['verified'];
                        $edited = true;
                    }
                }

                if ($data['Tweet']['verified'] == 2) {
                    $improve = true;
                } else {
                    $improve = false;
                }


                if ($original['Tweet']['verified'] != $data['Tweet']['verified'] && $data['Tweet']['verified'] == 1) {
                    $proofed = true;
                } else {
                    $proofed = false;
                }


                if (!isset($save['Tweet']['verified'])) {
                    $save['Tweet']['verified'] = $original['Tweet']['verified'];
                } else {
                    if ($save['Tweet']['verified'] == 1 && !empty($data['Tweet']['tweet_bank_id']) && $edited == false) {//tweet bank id present, not edited
                        $save['Tweet']['tweet_bank_id'] = $original['Tweet']['tweet_bank_id'];
                    } elseif ($save['Tweet']['verified'] == 1 && !empty($data['Tweet']['tweet_bank_id']) && $edited == true) {//tweet bank id present, edited
                        $save['Tweet']['tweet_bank_id'] = $data['Tweet']['tweet_bank_id'];
                    } elseif ($save['Tweet']['verified'] == 1 && empty($data['Tweet']['tweet_bank_id'])) {//new tweet with no tweet bank id
                        $newTweetBank = true;
                    } elseif ($save['Tweet']['verified'] == 1 && $edited == true) {//edited tweet with no tweet bank id
                        $newTweetBank = true;
                    }
                }

                //Image Handling
                if (!empty($data['Tweet']['img_url1']['name']) && empty($save['Tweet']['img_url'])) {
                    if ($x = $this->imageHandling($data['Tweet'])) {
                        $save['Tweet']['img_url'] = $x;
                        $edited = true;
                        $newTweetBank = true;
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
                } elseif (!empty($data['Tweet']['img_url2'])) {
                    $z = explode(".", $data['Tweet']['img_url2']);
                    $extension = end($z);
                    $allowed_extensions = array("gif", "jpeg", "jpg", "png");


                    $arrContextOptions=array(
                        "ssl"=>array(
                            //"cafile"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.ca-bundle",
                            //"local_cert"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.pem",
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        ),
                    ); 
                
                    if (in_array(strtolower($extension), $allowed_extensions)) {
                        $newFileName = $this->Session->read('Auth.User.id') . "-" . $data['Tweet']['account_id'] . "-" . $data['Tweet']['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                        $save['Tweet']['img_url'] = '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName;
                        if (copy($data['Tweet']['img_url2'], $save['Tweet']['img_url'], stream_context_create($arrContextOptions))) {
                            $save['Tweet']['img_url'] = 'https://tweetproof.com/img/uploads/' .$newFileName;
                            $edited = true;
                            $newTweetBank = true;
                        } else {
                            unset($save['Tweet']['img_url']);
                            $this->Session->setFlash('Image failed to upload. Please try again');
                        }
                    } else {
                        $this->Session->setFlash('You can only use images');
                    }
                } else {
                    if (empty($data['img_url'])) {
                        $data['img_url'] = $original['Tweet']['img_url'];
                    } else {
                        $save['img_url'] = $data['img_url'];
                    }
                }

                //error if tweet is over 140 chars
                if (!empty($save['Tweet']['img_url'])) {
                    if (strlen($save['Tweet']['body']) > 117 && $save['Tweet']['verified'] == 1) {
                        $save['Tweet']['verified'] = 0;
                    }
                } else {
                    if (strlen($save['Tweet']['body']) > 140 && $save['Tweet']['verified'] == 1) {
                        $save['Tweet']['verified'] = 0;
                    }
                }

                if (isset($newTweetBank)) {
                    if (!empty($calendar['EditorialCalendar']['bank_category_id'])) {
                        $save['TweetBank']['bank_category_id'] = $calendar['EditorialCalendar']['bank_category_id'];
                        $save['TweetBank']['body'] = $save['Tweet']['body'];
                        if (!empty($save['Tweet']['img_url'])) {
                            $save['TweetBank']['img_url'] = $save['Tweet']['img_url'];
                        }
                    }
                }

                foreach ($original['Editor'] as $editor) {
                    if ($editor['type'] == 'written') {
                        $written_by = $editor['user_id'];
                    }
                }
                

                if ($improve) {
                    $save['Editor'][] = array('type' => 'improve', 'user_id' => $this->Session->read('Auth.User.id'));
                } else {
                    if ($edited) {
                        if (!empty($written_by)) {
                            if ($written_by != $this->Session->read('Auth.User.id')) {
                                $save['Editor'][] = array('type' => 'edited', 'user_id' => $this->Session->read('Auth.User.id'));
                            }
                        } else {
                            $save['Editor'][] = array('type' => 'edited', 'user_id' => $this->Session->read('Auth.User.id'));
                        }
                    } 
                }

                if ($proofed) {
                    $save['Editor'][] = array('type' => 'proofed', 'user_id' => $this->Session->read('Auth.User.id'));
                }


                if (!empty($save['Tweet']['body'])) {
                    //$this->Tweet->save($toSave);
                    if ($save['Tweet']['verified'] == 1 && $save['Tweet']['timestamp'] > time()) {
                        //$this->CronTweet->save($key);
                        $CronTweet[] = $save['Tweet'];
                    } elseif ($save['Tweet']['verified'] != 1 && $original['Tweet']['verified'] == 1) {
                        $this->CronTweet->delete($data['Tweet']['id']);
                    }
                }

                if (!$edited) {
                    unset($save);
                } else {
                    $saveAll[] = $save;
                }

            } elseif (!ctype_digit($data['Tweet']['id']) && !empty($data['Tweet']['body'])) { //New Tweets
                $save['Tweet']['body'] = trim($data['Tweet']['body']);
                $save['Tweet']['first_name'] = $this->Session->read('Auth.User.first_name');

                $save['Tweet']['calendar_id'] = $data['Tweet']['calendar_id'];
                $save['Tweet']['time'] = $data['Tweet']['time'];
                $save['Tweet']['timestamp'] = strtotime($data['Tweet']['time']);
                $save['Tweet']['user_id'] = $this->Session->read('Auth.User.id');
                $save['Tweet']['account_id'] = $this->Session->read('access_token.account_id');


                $save['Editor'][] = array(
                    "user_id" => $this->Session->read('Auth.User.id'),
                    "type" => "written"
                    );


                if (empty($data['Tweet']['verified'])) {
                    $save['Tweet']['verified'] = 0;
                }


                if (empty($data['Tweet']['img_url'])) {
                    unset($save['Tweet']['img_url']);
                }

                //Image Handling

                if (!empty($data['Tweet']['img_url1']['name']) && empty($data['Tweet']['img_url'])) {
                    if ($x = $this->imageHandling($data['Tweet'])) {
                        $save['Tweet']['img_url'] = $x;
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
                } elseif (!empty($data['Tweet']['img_url2'])) {
                    $z = explode(".", $data['Tweet']['img_url2']);
                    $extension = end($z);
                    $allowed_extensions = array("gif", "jpeg", "jpg", "png");debug($extension);


                    $arrContextOptions=array(
                        "ssl"=>array(
                            //"cafile"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.ca-bundle",
                            //"local_cert"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.pem",
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        ),
                    ); 
                
                    if (in_array(strtolower($extension), $allowed_extensions)) {
                        $newFileName = $this->Session->read('Auth.User.id') . "-" . $save['Tweet']['account_id'] . "-" . $save['Tweet']['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                        $save['Tweet']['img_url'] = '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName;
                        if (copy($data['Tweet']['img_url2'], $save['Tweet']['img_url'])) {
                            $save['Tweet']['img_url'] = 'https://tweetproof.com/img/uploads/' .$newFileName;
                        } else {
                            unset($save['Tweet']['img_url']);
                            $this->Session->setFlash('Image failed to upload. Please try again');
                        }
                    } else {
                        $this->Session->setFlash('You can only use images');
                    }
                }

                if (empty($data['Tweet']['tweet_bank_id']) && $data['Tweet']['verified'] == 1) {
                    if (!empty($calendar['EditorialCalendar']['bank_category_id'])) {
                        $save['TweetBank']['bank_category_id'] = $calendar['EditorialCalendar']['bank_category_id'];
                        $save['TweetBank']['body'] = $save['Tweet']['body'];
                        if (!empty($save['Tweet']['img_url'])) {
                            $save['TweetBank']['img_url'] = $save['Tweet']['img_url'];
                        }
                    }
                    unset($save['Tweet']['tweet_bank_id']);
                } elseif (!empty($data['Tweet']['tweet_bank_id']) && $data['Tweet']['verified'] != 1) {
                    $save['Tweet']['tweet_bank_id'] = $data['Tweet']['tweet_bank_id'];
                } else {
                    $save['Tweet']['tweet_bank_id'] = 0;
                }

                if ($save['Tweet']['body']) {
                    if ($save['Tweet']['verified'] == 1 && $save['Tweet']['timestamp'] > time()) {
                        $CronTweet[] = $save['Tweet'];
                    }
                }

                $saveAll[] = $save;

            } else {
                $this->Tweet->delete($data['Tweet']['id']);
                $this->CronTweet->delete($data['Tweet']['id']);
                $edited = false;
            }
            unset($data);
            unset($original);
            unset($calendar);
            unset($id);
            unset($edited);
            unset($newTweetBank);
            unset($save);
            unset($improve);
            unset($proofed);
            unset($editor);
        }
        

        if (!empty($saveAll)) {
            if ($this->Tweet->saveAll($saveAll, array('deep' => true))) {

                $this->response->statusCode(200);

            } else {
                $errors = $this->Tweet->invalidFields();
                debug($errors);
                if (!empty($errors)) {
                    $this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again');
                    $this->response->statusCode(500);
                    $this->response->body(json_encode($errors, JSON_PRETTY_PRINT));
                }
            }
        }


        if (!empty($CronTweet)) {
            if ($this->CronTweet->saveAll($CronTweet)) {
            
                $this->response->statusCode(200);

            } else {
                //$this->Session->setFlash('Something went wrong, your tweets were not saved. Please try again1');
                $this->response->statusCode(500);
            }
        }

        //$this->response->body(json_encode($save, JSON_PRETTY_PRINT));
        return $this->response;
    }


    /*public function editcalendartweet1() {
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
                    } elseif ($key['verified'] == 1 && $key['forceVerified'] == 'true') {
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
                    } elseif ($original['Tweet']['verified'] == $key['verified'] && !empty($key['img_url'])) {//only changed image

                        $key['verified'] = 0;
                        $edited = true;
                    }

                    if ($original['Tweet']['verified'] == $key['verified'] && !empty($key['img_url2'])) {

                        $key['verified'] = 0;
                        $edited = true;
                        $uneditedTweet = 0;
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

                if (!empty($key['img_url1']['name']) && empty($key['img_url'])) {
                    if ($x = $this->imageHandling($key)) {
                        $key['img_url'] = $x;
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
                    $edited = true;
                } elseif (!empty($key['img_url2'])) {
                    $z = explode(".", $key['img_url2']);
                    $extension = end($z);
                    $allowed_extensions = array("gif", "jpeg", "jpg", "png");


                    $arrContextOptions=array(
                        "ssl"=>array(
                            //"cafile"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.ca-bundle",
                            //"local_cert"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.pem",
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        ),
                    ); 
                
                    if (in_array(strtolower($extension), $allowed_extensions)) {
                        $newFileName = $this->Session->read('Auth.User.id') . "-" . $key['account_id'] . "-" . $key['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                        $key['img_url'] = '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName;
                        if (copy($key['img_url2'], $key['img_url'], stream_context_create($arrContextOptions))) {
                            $key['img_url'] = 'https://tweetproof.com/img/uploads/' .$newFileName;
                        } else {
                            unset($key['img_url']);
                            $this->Session->setFlash('Image failed to upload. Please try again');
                        }
                    } else {
                        $this->Session->setFlash('You can only use images');
                    }
                } else {
                    if (empty($key['img_url'])) {
                        $key['img_url'] = $original['Tweet']['img_url'];
                    }
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

                if (!empty($key['tweet_bank_id'])) {

                } else {
                    if (!empty($newTweetBank)) {
                        if (!empty($calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'])) {
                            $toSave['TweetBank']['bank_category_id'] = $calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'];
                            $toSave['TweetBank']['body'] = $key['body'];
                            if (!empty($key['img_url'])) {
                                $toSave['TweetBank']['img_url'] = $key['img_url'];
                            }
                        }
                    }
                    unset($key['tweet_bank_id']);
                }

                $toSave['Tweet']['id'] = $key['id'];
                $toSave['Tweet']['body'] = $key['body'];
                $toSave['Tweet']['verified'] = $key['verified'];
                $toSave['Tweet']['account_id'] = $key['account_id'];
                $toSave['Tweet']['timestamp'] = $key['timestamp'];
                $toSave['Tweet']['time'] = $key['time'];
                if (!empty($key['tweet_bank_id'])) {
                    $toSave['Tweet']['tweet_bank_id'] = $key['tweet_bank_id'];
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

                if (!empty($key['img_url1']['name']) && empty($key['img_url'])) {
                    if ($x = $this->imageHandling($key)) {
                        $key['img_url'] = $x;
                    } else {
                        //$this->Session->setFlash('There was an error processing your image, please try again.');
                    }
                } elseif (!empty($key['img_url2'])) {
                    $z = explode(".", $key['img_url2']);
                    $extension = end($z);
                    $allowed_extensions = array("gif", "jpeg", "jpg", "png");debug($extension);


                    $arrContextOptions=array(
                        "ssl"=>array(
                            //"cafile"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.ca-bundle",
                            //"local_cert"=>"/var/www/clients/client1/web8/web/app/webroot/ssl/tweetproof.com.pem",
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        ),
                    ); 
                
                    if (in_array(strtolower($extension), $allowed_extensions)) {
                        $newFileName = $this->Session->read('Auth.User.id') . "-" . $key['account_id'] . "-" . $key['calendar_id'] . "-" . md5(mt_rand(100000,999999)) . "." . $extension;
                        $key['img_url'] = '/var/www/clients/client1/web8/web/app/webroot/img/uploads/'.$newFileName;
                        if (copy($key['img_url2'], $key['img_url'])) {
                            $key['img_url'] = 'https://tweetproof.com/img/uploads/' .$newFileName;
                        } else {
                            unset($key['img_url']);
                            $this->Session->setFlash('Image failed to upload. Please try again');
                        }
                    } else {
                        $this->Session->setFlash('You can only use images');
                    }
                }

                if (!empty($key['tweet_bank_id'])) {

                } else {
                    if (!empty($calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id']) && $key['verified'] == 1) {
                        $key['TweetBank']['bank_category_id'] = $calendars[$key['calendar_id']]['EditorialCalendar']['bank_category_id'];
                        $key['TweetBank']['body'] = $key['body'];
                        if (!empty($key['img_url'])) {
                            $key['TweetBank']['img_url'] = $key['img_url'];
                        }
                    }
                    unset($key['tweet_bank_id']);
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
                if (!empty($toSave['Tweet']['tweet_bank_id'])) {
                    $toSave['Tweet']['tweet_bank_id'] = $key['tweet_bank_id'];
                }
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
            unset($toSave);
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
    */

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

                $key['img_url'] = "https://tweetproof.com/img/uploads/".$newFileName;
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


    public function calendarRefresh ($months = 0) {
        $this->set('months', $months);
        //Check if user is admin of team
        $team = $this->Cookie->read('currentTeam');
        if ($this->TeamsUser->hasAny(array('team_id' => $team, 'user_id' => $this->Session->read('Auth.User.id'), 'group_id' => 1))) {
            $this->set('isTeamAdmin', true);
        } else {
            $this->set('isTeamAdmin', false);
        }

        //Month selector at top of page
        $base = strtotime(date('Y-m',time()) . '-01 00:00:01'); 
        $this->set('base', $base);
        $monthsarray = array(
            -5 => date('F Y', strtotime('-5 month', $base)),
            -4 => date('F Y', strtotime('-4 month', $base)),
            -3 => date('F Y', strtotime('-3 month', $base)),
            -2 => date('F Y', strtotime('-2 month', $base)),
            -1 => date('F Y', strtotime('-1 month', $base)),
            0 => date('F Y', strtotime('+0 month', $base)),
            1 => date('F Y', strtotime('+1 month', $base)),
            2 => date('F Y', strtotime('+2 month', $base)),
            3 => date('F Y', strtotime('+3 month', $base)),
            4 => date('F Y', strtotime('+4 month', $base)),
            5 => date('F Y', strtotime('+5 month', $base))
            );

        $this->set('monthsarray', $monthsarray);

        //Grab editorial calendars (check if user is in a month in the past or future)
        $this->Session->write('Auth.User.monthSelector', $months);
        if ($months >= 0) {
            $tweetConditions = array(
                                'timestamp >=' => strtotime(date('M Y') . ' + ' . ($months) . 'months'), 
                                'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months')
                                );
        } elseif ($months < 0) {
            $tweetConditions = array(
                                'timestamp >=' => strtotime(date('M Y') . ' - ' . (abs($months)) . 'months'), 
                                'timestamp <=' => strtotime(date('M Y') . ' - ' . abs(($months + 1)) . 'months')
                                );
        }

        $calendar = $this->EditorialCalendar->find(
            'all', 
            array('recursive' => 1, 
                'conditions' => array(
                    'twitter_account_id' => $this->Session->read('access_token.account_id')
                    ), 
                'order' => array('EditorialCalendar.time' => 'ASC'),
                'contain' => array(
                    'TwitterAccount',
                    'BankCategory'
                    )
                )
            );

        $calendarx = array();
        foreach ($calendar as $key) {
            $calendarx[$key['EditorialCalendar']['time']][$key['EditorialCalendar']['day']] = $key;
        }
        $calendar = $calendarx;

        $this->set('calendar', $calendar);

        //Generate array of all days in month
        $daysinmonth = (int)date('t', strtotime('+' . $months . ' month', $base));
        $days = array();
        $month = date('m', strtotime('+' . $months . ' month', $base));
        if ($months == 0) {
            $day = date('d');
        } elseif ($months !== 0) {
            $day = 1;
        } 
        $year = date('Y');


        for ($i=$day; $i<=$daysinmonth; $i++) {
            $days[date('d-m-Y',mktime(0,0,0,$month,$i,$year))] = date('l',mktime(0,0,0,$month,$i,$year));
        }
        $this->set('days', $days);
        $this->layout = '';
    }

    public function oldCalendarRefresh($months) {        
        $this->Session->write('Auth.User.monthSelector', $months);
        if ($months >= 0) {
            $calendar = $this->EditorialCalendar->find('all', array('recursive' => 1, 'conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC'), 'contain' => array('TwitterAccount', 'BankCategory', 'Tweet' => array('conditions' => array('timestamp >=' => strtotime(date('M Y') . ' + ' . ($months) . 'months'), 'timestamp <=' => strtotime(date('M Y') . ' + ' . ($months + 1) . 'months')), 'order' => array('Tweet.timestamp' => 'ASC'), 'Comment', 'Editor' => array('User')))));
        } elseif ($months < 0) {
            $calendar = $this->EditorialCalendar->find('all', array('recursive' => 1, 'conditions' => array('twitter_account_id' => $this->Session->read('access_token.account_id')), 'order' => array('EditorialCalendar.time' => 'ASC'), 'contain' => array('TwitterAccount', 'BankCategory', 'Tweet' => array('conditions' => array('timestamp >=' => strtotime(date('M Y') . ' - ' . (abs($months)) . 'months'), 'timestamp <=' => strtotime(date('M Y') . ' - ' . abs(($months + 1)) . 'months')), 'order' => array('Tweet.timestamp' => 'ASC'), 'Comment', 'Editor' => array('User')))));
        }
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
        $this->set('session_teams', Hash::combine($this->Session->read('Auth.User.Team'), '{n}.id', '{n}'));
        

        if (isset($months)) {
            $this->set('months', $months);
        }

        $team = $this->Cookie->read('currentTeam');
        $this->set('team', $team);
        if ($this->TeamsUser->hasAny(array('team_id' => $team, 'user_id' => $this->Session->read('Auth.User.id'), 'group_id' => 1))) {
            $this->set('isTeamAdmin', 1);
        } else {
            $this->set('isTeamAdmin', 0);
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


    public function recycle($calendar_id = null) {
        $teamIDs = array();
        foreach ($this->Session->read('Auth.User.Team') as $key) {
            $teamIDs[] = $key['id'];
        }
        $accounts = $this->TwitterPermission->find('list', array('fields' => 'twitter_account_id', 'conditions' => array('team_id' => $teamIDs)));
        $screen_names = $this->TwitterAccount->find('list', array('fields' => 'screen_name', 'conditions' => array('account_id' => $accounts), 'order' => array('screen_name' => 'ASC')));
        $all_accounts = $this->TwitterAccount->find('list', array('fields' => array('account_id', 'screen_name'), 'conditions' => array('account_id' => $accounts), 'order' => array('screen_name' => 'ASC')));
        $this->set('accounts', $screen_names);
        $this->set('all_accounts', $all_accounts);
        

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
            if (!empty($categoriesx[$calendar_id])) {
                $this->set('selectedCategories', $this->request->data['BankCategory']);
                $tweetBanks = $this->TweetBank->find('all', array('conditions' => array('bank_category_id' => $this->request->data['BankCategory'])));
            } else {
                $this->set('selectedCategories', $categories[0]['BankCategory']['id']);
                $tweetBanks = $this->TweetBank->find('all', array('conditions' => array('bank_category_id' => $categories[0]['BankCategory']['category'])));
            }
            
        } else {
            if (!empty($calendar_id)) {
                $calendar = $this->EditorialCalendar->find('first', array('conditions' => array('EditorialCalendar.id' => $calendar_id)));
                if (!empty($calendar)) {
                    $bank_category_id = $calendar['BankCategory']['id'];
                    $this->set('selectedCategories', $bank_category_id);
                    $tweetBanks = $this->TweetBank->find('all', array('conditions' => array('bank_category_id' => $bank_category_id)));
                } else {
                    $this->set('selectedCategories', '');
                    $tweetBanks = array();
                }
            } else {
                $this->set('selectedCategories', '');
                $tweetBanks = array();
            }
        }

        $tweet_bank_ids = array();
        foreach ($tweetBanks as $key) {
            $tweet_bank_ids[] = $key['TweetBank']['id'];
        }
        $tweet_bank_counts = $this->Tweet->find('all', array('fields' => array("COUNT(tweet_bank_id)", 'tweet_bank_id'), 'conditions' => array('tweet_bank_id' => $tweet_bank_ids), 'group' => 'tweet_bank_id'));
        $tweet_bank_counts = Hash::combine($tweet_bank_counts, '{n}.Tweet.tweet_bank_id', '{n}');
        $this->set('tweet_bank_counts', $tweet_bank_counts);


        $this->set('tweetBanks', $tweetBanks);
        $this->layout = '';
    }

    public function tweet($calendar_id, $timestamp) {
        $calendar = $this->EditorialCalendar->find('first', 
            array(
                'conditions' => array(
                    'twitter_account_id' => $this->Session->read('access_token.account_id'),
                    'EditorialCalendar.id' => $calendar_id
                    ),
                'contain' => array(
                    'TwitterAccount',
                    'BankCategory',
                    'Tweet' => array(
                        'conditions' => array(
                            'timestamp' => $timestamp
                            ),
                        'Comment',
                        'Editor' => array(
                            'User'
                            )
                        )
                    )
                )
            );
        if (!empty($calendar['Tweet'])) {
            $tweet = $calendar['Tweet'][0];
            $tweet['BankCategory'] = $calendar['BankCategory'];
            $tweet['EditorialCalendar'] = $calendar['EditorialCalendar'];
            unset($calendar);

            $obj['idForPusher'] = $tweet['id'];
            if (!empty($tweet['img_url'])) {
                $obj['withImage'] = 'withImage';
            } else {
                $obj['withImage'] = 'withoutImage';
            }
            $obj['commentCount'] = count($tweet['Comment']);
            $obj['calendarTime'] = date('d-m-Y H:i', $timestamp);
            $obj['fullDate'] = date('jS F Y', strtotime($timestamp));
            $obj['present'] = 'present';
        } else {
            $tweet['id'] = 'a' . substr(md5(rand()), 0, 7);
            $tweet['body'] = "";
            $tweet['account_id'] = $calendar['EditorialCalendar']['twitter_account_id'];
            $tweet['calendar_id'] = $calendar_id;
            $tweet['time'] = date('d-m-Y H:i', $timestamp);
            $tweet['timestamp'] = $timestamp;
            $tweet['verified'] = 0;
            $tweet['published'] = 0;
            $tweet['tweet_bank_id'] = "";
            $tweet['BankCategory'] = $calendar['BankCategory'];
            $tweet['EditorialCalendar'] = $calendar['EditorialCalendar'];
            unset($calendar);

            $obj['idForPusher'] = $tweet['id'];
            $obj['withImage'] = 'withoutImage';
            $obj['commentCount'] = 0;
            $obj['calendarTime'] = $tweet['time'];
            $obj['fullDate'] = date('jS F Y', strtotime($timestamp));
            $obj['present'] = '';

        }


        $team = $this->Cookie->read('currentTeam');
        if ($this->TeamsUser->hasAny(array('team_id' => $team, 'user_id' => $this->Session->read('Auth.User.id'), 'group_id' => 1))) {
            $this->set('isTeamAdmin', true);
            $obj['disabled'] = '';
        } else {
            $this->set('isTeamAdmin', false);
            $obj['disabled'] = 'disabled';
        }

        $this->set('tweet', $tweet);
        $this->set('obj', $obj);

        $this->layout = '';
    }
}