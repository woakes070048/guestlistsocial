<?php
class CommentsController extends AppController {
	public $components = array('Session', 'Auth');
    public $helpers =  array('Html' , 'Form', 'Session');

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
		$this->Comment->saveAll($this->request->data);
        $this->redirect(Controller::referer());
	}
}