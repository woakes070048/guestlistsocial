<?php
App::import('Vendor', 'OAuth/OAuthClient');


class HelloShell extends AppShell {
    public $uses = array('TwitterAccount', 'CronTweet', 'Tweet');

    public function main() {
        $toTweet = $this->Tweet->find('all', array('conditions' => array('id' => '')));

        $accountID = $toTweet[0]['Tweet']['account_id'];
        $accountDetails = $this->TwitterAccount->find('all', array('conditions' => array('account_id' => $accountID)));

        $oauth_token = $accountDetails[0]['TwitterAccount']['oauth_token'];
        $oauth_token_secret = $accountDetails[0]['TwitterAccount']['oauth_token_secret'];
        $client = $this->createClient();

        if ($oauth_token&&$oauth_token_secret) {
            if ($toTweet[$i]['Tweet']['img_url']) {
                $client->postMultipartFormData($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update_with_media.json', array('media[]' => $toTweet[$i]['Tweet']['img_url']), array('status' => $toTweet[$i]['Tweet']['body']));
            } else {
                debug($client->post($oauth_token, $oauth_token_secret, 'https://api.twitter.com/1.1/statuses/update.json', array('status' => $toTweet[$i]['Tweet']['body'])));
            }
            $this->Tweet->id = $toTweet[$i]['Tweet']['id'];
            $this->Tweet->saveField('published', 1);
            $this->out('Complete');
        }
    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }
}