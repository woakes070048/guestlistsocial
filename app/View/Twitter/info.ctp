<?php
echo $this->Form->create('TwitterAccount');
echo $this->Form->input('Link', array('name' => 'data[TwitterAccount][infolink]', 'value' => 'https://'));
echo $this->Form->end('Submit');

echo $this->Html->link('Skip', '/twitter/');