<?php
//Grabs new info from twitter servers
App::import('Vendor', 'OAuth/OAuthClient');

class RefreshDataShell extends AppShell {
	public $uses = array('TwitterAccount', 'CronTweet', 'Tweet', 'Statistic');

    public function main() {
    	$accounts = $this->TwitterAccount->find('all', array('recursive' => 0));
        $accounts = Hash::combine($accounts, '{n}.TwitterAccount.screen_name', '{n}');
        $client = $this->createClient();
        $toSave = array();
        $toSave1 = array();
        /*$i = 0;
        foreach ($accounts as $key) {
            $i++;
            $x = array();
            $x1 = array();
            $name = $key['TwitterAccount']['screen_name'];
            $oauth_token = null;
            $oauth_token_secret = null;
            $details = json_decode($client->get($oauth_token, $oauth_token_secret, "https://api.twitter.com/1.1/users/show.json?screen_name=$name"), true);
            $x1['TwitterAccount']['account_id'] = $key['TwitterAccount']['account_id'];
            $x1['TwitterAccount']['profile_pic'] = $details['profile_image_url'];
            $x1['Statistic']['twitter_account_id'] = $key['TwitterAccount']['account_id'];
            $x1['Statistic']['time'] = date('d-m-Y H:i', time());
            $x1['Statistic']['timestamp'] = time();
            $x1['Statistic']['followers_count'] = $details['followers_count'];
            $x1['Statistic']['following_count'] = $details['friends_count'];
            $x1['Statistic']['favourites_count'] = $details['favourites_count'];
            if (!empty($details['errors'])) {
                echo $details['errors'][0]['code'];
                echo $key['TwitterAccount']['account_id'];
            } else {
                $toSave1[] = $x1;
            }
            if (!empty($details['errors']) && $details['errors'][0]['code'] == 88) {
                break;
            }

        }debug($i);*/
        foreach ($accounts as $key) {
        	$twitter_request["screen_name"][] = $key['TwitterAccount']['screen_name'];
        }

        $account_number = count($accounts);
        $number_of_calls = ceil($account_number/100);
        $k = 0;
        for ($i = 1; $i <= $number_of_calls; $i++) {
            $twitter_request1["screen_name"] = array_slice($twitter_request["screen_name"], ($i * 100) - 100, 100);
            $details = json_decode($client->post(null, null, "https://api.twitter.com/1.1/users/lookup.json", $twitter_request1), true);
            //debug($details);
            $k++;
            foreach ($details as $key) {
                if (!empty($key["screen_name"])) {
                    $screen_name = $key["screen_name"];
                    debug($screen_name);
                    debug($k);
                    $x1['TwitterAccount']['account_id'] = $accounts[$screen_name]['TwitterAccount']['account_id'];
                    $x1['TwitterAccount']['profile_pic'] = $key['profile_image_url_https'];
                    $x1['Statistic']['twitter_account_id'] = $accounts[$screen_name]['TwitterAccount']['account_id'];
                    $x1['Statistic']['time'] = date('d-m-Y H:i', time());
                    $x1['Statistic']['timestamp'] = time();
                    $x1['Statistic']['followers_count'] = $key['followers_count'];
                    $x1['Statistic']['following_count'] = $key['friends_count'];
                    $x1['Statistic']['favourites_count'] = $key['favourites_count'];
                    $toSave1[] = $x1;
                } else {
                    debug($details["errors"]);
                    debug($k);
                }

            }
            unset($details);
        }
        $this->Statistic->saveAll($toSave1, array('deep' => true));

    }

    private function createClient() {
        return new OAuthClient('eyd9m3ROB8RT6ZGhfM0xYg', 'VVjdqpQjvpVCXAqSYQWHFGRCpAQKTs0v2zYULbgohjU');
    }
}