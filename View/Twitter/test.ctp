<? echo $this->Form->create('Tweet', array('type' => 'file'));
echo $this->Form->input('Tweet.img_url', array('type' => 'file'));
echo $this->Form->input('Tweet.img_url_dir', array('type' => 'hidden'));
echo $this->Form->end('Submit');