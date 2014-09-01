<tr><td style="border: none"><?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit', 'type' => 'file'));?>
<table id="refresh">
<thead class="mainheader">
    <th class='sort'><? echo $this->Paginator->sort('timestamp', 'Schedules');?></th>
    <th class='sort'><? echo $this->Paginator->sort('screen_name', 'Account');?></th>
    <th class='sort'><? echo $this->Paginator->sort('first_name', 'Written By');?></th>
    <th class='sort'><? echo $this->Paginator->sort('body', 'Tweet');?></th>
    <th>Verified</th>
    <th></th>
</thead>
    <?php foreach ($tweets as $key) { ?>
    <?php if ($key['Tweet']['verified'] == 1) {
            $checked = 'checked';
            $value = $key['Tweet']['time'];
            $color = '#ffb400';
        } elseif ($key['Tweet']['verified'] == 1 && $key['Tweet']['client_verified'] == 1) {
            $color = 'Green';
        } else {
            $checked = '';
            $value = '';
            $color = 'Red';} 

            if ($this->Session->read('Auth.User.group_id') == 2) {
                $disabled = 'disabled';
            } else {
                $disabled = '';
            }?>
    <tr>
      <td class='scheduled' id='time<?php echo $key['Tweet']['id']?>'> 
        <div class='notediting'><?php if($key['Tweet']['time'] && $key['Tweet']['published'] == 1) {
            echo $key['Tweet']['time'] . '<small>[Published]</small>';
            } elseif ($key['Tweet']['time']) {
                echo $key['Tweet']['time'];
            } else {
                echo '';
                } ?>
        </div>
        <?php if($key['Tweet']['published'] == 0) {
            echo $this->Form->input('timestamp', array(
            'type' => 'text', 
            'label' => false, 
            'class' => 'schedule',
            'value' => $key['Tweet']['time'], 
            'id' => 'schedule'.$key['Tweet']['id'], 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][timestamp]',
            'style' => 'display: none'
            ));
            }?>
      </td>
      <td>
        <? echo $key['Tweet']['screen_name']; ?>
      </td>
      <td class='writtenBy'>
        <?php echo $key['Tweet']['first_name']; ?>
      </td>
      <td class='nopadding' id=<?php echo $key['Tweet']['id']?>>
        <?php echo $this->Form->textarea('body', array(
            'class' => 'editing', 
            'value' => $key['Tweet']['body'], 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][body]', 
            'label' => false, 
            'maxlength' => '140')); ?> 
            
            <div class="tweetButtons">
            <? echo $this->Form->button('Shorten URLs', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
            <? if ($key['Tweet']['img_url']) {
                    echo $this->Html->image($key['Tweet']['img_url']);
                }?>
            <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][img_url1]', 'label' => false)); ?>
            <? echo $this->Form->button('Delete', array('type' => 'button', 'class' => 'delete', 'id' => $key['Tweet']['id'])); ?>
            <? echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'smallSaveButton'));?>
            </div>
      </td>
      <td class='verified'>
        <?php echo $this->Form->input('verified', array(
        'type' => 'radio', 
        'options' => array(
            1 => 'APPROVED', 
            0 => 'AWAITING APPROVAL', 
            2 => 'IMPROVE'
            ), 
        'legend' => false, 
        'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified]', 
        'class' => 'TwitterVerified1', 
        'id' => $key['Tweet']['id'], 
        'default' => $key['Tweet']['verified']));?>
      </td>
      <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['Tweet']['id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][id]'));
            echo $this->Form->input('verfied_by', array(
            'type' => 'hidden', 
            'value' => $this->Session->read('Auth.User.first_name'), 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified_by]', 
            'class' => 'verifiedby', 
            'id' => $key['Tweet']['id'] . '_' . $this->Session->read('Auth.User.first_name')));
            echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $key['Tweet']['user_id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][user_id]'));
            echo $this->Form->input('account_id', array('type' => 'hidden', 'value' => $key['Tweet']['account_id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][account_id]'));?>
    </tr>
    <?php } ?>
</table>

<?php echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save')); ?></td></tr>
<tr><td>
<?echo $this->Paginator->numbers();?>
</td></tr>