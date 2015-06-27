<?php
App::uses('AuthComponent', 'Controller/Component');

class Notification extends AppModel {
	public $belongsTo = array(
        'User' => array(
            'className' => 'User'
        )
    );
    public $actsAs = array('Pusher.Pusher');

    public function add($user_id, $notification, $read = 0, $icon = null) {
        $data = array();
        $data['Notification']['user_id'] = $user_id;
        $data['Notification']['notification'] = $notification;
        $data['Notification']['read'] = $read;
        $data['Notification']['timestamp'] = time();
        if ($icon) {
            $data['Notification']['icon'] = $icon;
        }
        $this->create();
        if ($this->save($data)) {
            return true;
        } else {
            return false;
        }
    }

    public function markAsRead($notifications) {
        if (is_array($notifications)) {
            foreach ($notifications as $key) {
                $array[] = array('id' => $key['Notification']['id'], 'read' => 1);
            }
    
            $this->saveAll($array);
        } else {
            $this->id = $notifications;
            $this->saveField('read', 1);
        }
    }
}