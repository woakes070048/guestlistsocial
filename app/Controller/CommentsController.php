<?php
class CommentsController extends AppController {
	public $components = array('Session', 'Auth', 'Pusher.Pusher');
    public $helpers =  array('Html' , 'Form', 'Session', 'Pusher.Pusher');
    var $uses = array('Tweet', 'Notification', 'Comment');

	public function commentRefresh($tweet_id = null) {
		if ($tweet_id) {
			$comments = $this->Comment->find('all', array('conditions' => array('tweet_id' => $tweet_id), 'order' => array('Comment.id' => 'ASC')));
			$this->set('comments', $comments);
			$this->set('tweet_id', $tweet_id);
		}

		$this->layout = '';
	}

	public function commentSave() {
		$this->request->data['Comment']['user_id'] = $this->Session->read('Auth.User.id');
		$this->request->data['Comment']['type'] = 'info';
		$this->Comment->saveAll($this->request->data);
		$data = $this->request->data;

		$tweet = $this->Tweet->find('first', array('conditions' => array('Tweet.id' => $data['Comment']['tweet_id'])));
		$uid = $tweet['Tweet']['user_id'];
		$screenName = $tweet['TwitterAccount']['screen_name'];
		$msg = 'A comment has been added to your tweet for <img src="/img/twitter19px.png" style="margin-top:-6px;vertical-align:middle;"><span style=\'color:#6ed3fd\'>@' . $screenName . '</span> at ' . $tweet['Tweet']['time'];
		$icon = $this->Session->read('Auth.User.profile_pic');
		$this->Notification->add($uid, $msg, 0, $icon);

		$this->Comment->trigger('private-comment_channel', 'new_comment', $data);
        $this->redirect(Controller::referer());
	}
}