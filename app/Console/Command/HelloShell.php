<?php
App::import('Vendor', 'OAuth/OAuthClient');


class HelloShell extends AppShell {
    public $uses = array('TwitterAccount', 'CronTweet', 'Tweet');

    public function main() {
        $toTweet = $this->Tweet->find('all', array('conditions' => array('Tweet.id' => 49518)));

        $accountID = $toTweet[0]['Tweet']['account_id'];
        $accountDetails = $this->TwitterAccount->find('all', array('conditions' => array('account_id' => $accountID)));

        $oauth_token = $accountDetails[0]['TwitterAccount']['oauth_token'];
        $oauth_token_secret = $accountDetails[0]['TwitterAccount']['oauth_token_secret'];
        $client = $this->createClient();

        if ($oauth_token&&$oauth_token_secret) {
            if ($toTweet[0]['Tweet']['img_url']) {
                $echo = $client->postMultipartFormData($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update_with_media.json', array('media[]' => $toTweet[0]['Tweet']['img_url']), array('status' => $toTweet[0]['Tweet']['body']));
            } else {
                $echo = $client->post($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $toTweet[0]['Tweet']['body']));
            }
            $this->Tweet->id = $toTweet[0]['Tweet']['id'];
            $x = json_decode($echo, true);
            if (!empty($x['errors'][0]['code'])) {
                foreach ($x['errors'] as $key) {
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
    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }
}