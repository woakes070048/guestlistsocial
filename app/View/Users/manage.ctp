<?
echo $this->Form->create('User');
echo $this->Form->input('first_name', array('value' => $firstname, 'label' => 'First Name', 'required' => false));
echo $this->Form->input('last_name', array('value' => $lastname, 'label' => 'Last Name'));
echo $this->Form->input('curr_password', array('type' => 'password', 'label' => 'Current Password', 'required' => false));
echo $this->Form->input('password', array('type' => 'password', 'label' => 'New Password', 'required' => false));
echo $this->Form->input('password2', array('type' => 'password', 'label' => 'Confirm Password', 'required' => false));
echo $this->Form->end('Submit');
?>