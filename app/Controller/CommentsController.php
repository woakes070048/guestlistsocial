<?php
class CommentsController extends AppController {
	public function commentRefresh($tweet_id) {
		$comments = $this->Comment->find('all', array('conditions' => array('tweet_id' => $tweet_id)));
		$this->set('comments', $comments);
	}

	public function commentSave() {
		
	}
}