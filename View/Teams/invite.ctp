<?echo $this->Form->create('invite');
echo $this->Form->input('team', array('Select team to invite to', 'options' => $dropdownteams));
echo $this->Form->input('email', array('Enter email'));
echo $this->Form->input('group', array('Select group', 'options' => array(2 => 'Team Member', 7 => 'Proofer', 1 => 'Admin')));
echo $this->Form->end('Send invite');
?>