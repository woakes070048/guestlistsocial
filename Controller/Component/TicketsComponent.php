<?php
App::uses('Component', 'Controller', 'Ticket');
class TicketsComponent extends Component {
	public function set($info = null) {
		$this->Ticket = ClassRegistry::init('Ticket');
		if ($info) {
			$data['Ticket']['hash'] = md5(mt_rand(1000000000,9999999999));
			$data['Ticket']['data'] = $info;

			$this->Ticket->create();
			if ($this->Ticket->save($data)) {
                return $data['Ticket']['hash'];
            }
         
		} 

		return false;
	}

	public function get($ticket = null) {
		$this->Ticket = ClassRegistry::init('Ticket');
		if ($ticket) {
			$data = $this->Ticket->find('first', array('conditions' => array('hash' => $ticket)));
			if ($data) {
				return $data['Ticket']['data'];
			}
		}

		return false;
	}

	public function del($ticket = null) {
		$this->Ticket = ClassRegistry::init('Ticket');
		if ($ticket) {
			$data = $this->Ticket->find('first', array('conditions' => array('hash' => $ticket)));
			if ($data) {
				$this->Ticket->delete($data['Ticket']['id']);
			}
		}

		return false;
	}
}

