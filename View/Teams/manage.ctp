
Create team:
<?php
	echo $this->Form->create('createTeam');
	echo $this->Form->input('Team name', array('name' => 'name'));
	echo $this->Form->end('Submit');

?>