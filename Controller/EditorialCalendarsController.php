<?php
class EditorialCalendarsController extends AppController {
    public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form', 'Session');
    var $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'User', 'TwitterPermission', 'EditorialCalendar');

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
                    }
                }
            }
        }
        $this->redirect(Controller::referer());
    }

    public function editcalendartweet() {
        foreach ($this->request->data['Tweet'] as $key) {
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

            if (isset($key['verified_by'])) {
                if ($key['verified'] == 2) {
                    $key['verified'] == 0;
                }
            }

            //if tweet is set to 'needs improving' and body has been changed, set back to 'awaiting proof'
            if (isset($tweet)) {
                if ($tweet['Tweet']['verified'] == 2 && $key['verified'] == 2) {
                    if ($tweet['Tweet']['body'] != $key['body']) {
                        $key['verified'] = 0;
                    }
                }
            }

            $key['first_name'] = $this->Session->read('Auth.User.first_name');

            $key['user_id'] = $this->Session->read('Auth.User.id');
            $key['account_id'] = $this->Session->read('access_token.account_id');

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
                $this->Tweet->delete($id);
                $this->CronTweet->delete($id);
            }
        }

        $this->redirect(Controller::referer());
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
?>