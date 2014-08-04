<?php
App::uses('Component', 'Controller', 'Ticket');
class TicketsComponent extends Component {
	public function set($info = null) {
		$this->Ticket = ClassRegistry::init('Ticket');
		if ($info) {
			$data['Ticket']['hash'] = md5(time());
			$data['Ticket']['data'] = $info;

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

