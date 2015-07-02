<?php
class NotificationsController extends AppController {
	public $components = array('Session', 'Auth', 'Pusher.Pusher', 'Paginator');
    public $helpers =  array('Html' , 'Form', 'Session', 'Pusher.Pusher');

    public function beforeFilter() {
        parent::beforeFilter();
		$this->Auth->allow(array('action' => 'notificationRefresh'));
    }

    public function notificationRefresh($user_id) {
    	$c = array('user_id' => $user_id);
    	$this->Paginator->settings = array(
        'conditions' => $c,
        'limit' => 5,
        'order' => array('timestamp' => 'DESC')
        );

        $notifications = $this->Paginator->paginate('Notification');
        $this->set('notifications', $notifications);

        $notificationCount = $this->Notification->find('count', array('conditions' => array('user_id' => $user_id, 'read' => 0)));
        $this->set('notificationCount', $notificationCount);


        $this->Notification->markAsRead($notifications);

        $this->layout = '';
    }

    public function delete($id) {
    	$notification = $this->Notification->find('first', array('conditions' => array('Notification.id' => $id)));
    	if ($this->Session->read('Auth.User.id') == $notification['Notification']['user_id']) {
    		$this->Notification->delete($id);
    		return 1;
    	} else {
    		return 0;
    	}
    }
}