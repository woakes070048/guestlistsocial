<?php
//Finds tweets from db with timestamp in last minute and sends them off
App::import('Vendor', 'OAuth/OAuthClient');

class ScheduleShell extends AppShell {
	public $uses = array('TwitterAccount', 'CronTweet', 'Tweet');

    public function main() {
        $lowerbound = time() - 120;
        $upperbound = time();
        $toTweet = $this->CronTweet->find('all', array('conditions' => array('timestamp <' => $upperbound, 'timestamp >' => $lowerbound)));
        $count = $this->CronTweet->find('count', array('conditions' => array('timestamp <' => $upperbound, 'timestamp >' => $lowerbound)));
        $toDelete = $this->CronTweet->find('list', array('fields' => array('id'), 'conditions' => array('timestamp <' => $upperbound, 'timestamp >' => $lowerbound)));

        if ($count != 0) {
            $count-=1;
           
            $i = 0;
            for ($i < $count; $i <= $count; $i++) {
                $accountID = $toTweet[$i]['CronTweet']['account_id'];
                $accountDetails = $this->TwitterAccount->find('all', array('conditions' => array('account_id' => $accountID)));

                $oauth_token = $accountDetails[0]['TwitterAccount']['oauth_token'];
                $oauth_token_secret = $accountDetails[0]['TwitterAccount']['oauth_token_secret'];
                $client = $this->createClient();

                if ($oauth_token&&$oauth_token_secret) {

                    if ($toTweet[$i]['CronTweet']['img_url']) {
                        $log = $client->postMultipartFormData($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update_with_media.json', array('media[]' => $toTweet[$i]['CronTweet']['img_url']), array('status' => $toTweet[$i]['CronTweet']['body']));
                    } else {
                        $log = $client->post($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $toTweet[$i]['CronTweet']['body']));
                    }
                    
                    $this->Tweet->id = $toTweet[$i]['CronTweet']['id'];
                    $json = json_decode($log, true);
                    if (!empty($json['errors'][0]['code'])) {
                        foreach ($json['errors'] as $key) {
                            $error_code = (int)$key['code'];
                            $this->out($error_code);
                            $this->log("
                                        id:" . $toTweet[0]['Tweet']['id'] . " not sent. Error code: " . $error_code);
                            $this->Tweet->saveField('error', $error_code);
                        }
                    } else {
                        $this->Tweet->saveField('published', 1);
                    }
                    $this->out('Complete');
                }
            $this->CronTweet->deleteAll(array('id' => $toDelete));

            }
        } elseif ($count == 0) {
            $this->out('No Tweets');
        }
    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }
}
