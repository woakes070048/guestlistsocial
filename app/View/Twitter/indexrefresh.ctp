<?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit', 'type' => 'file'));?>
<div id="refresh">
      <div style="display: inline">
        <span class='screenName'><? echo '@' . $key['Tweet']['screen_name']; ?></span>
      </div>
    <?php if ($key['Tweet']['verified'] == 1) {
            $checked = 'checked';
            $value = $key['Tweet']['time'];
            $color = 'Green';
        } elseif ($key['Tweet']['verified'] == 1 && $key['Tweet']['client_verified'] == 1) {
            $color = 'Green';
        } else {
            $checked = '';
            $value = '';
            $color = 'Red';} 

            if ($this->Session->read('Auth.User.group_id') == 2 || $status == 'published') {
                $disabled = 'disabled';
            } else {
                $disabled = '';
            }?>
      <div class='verified'>
        <?php echo $this->Form->input('verified', array(
        'type' => 'select', 
        'options' => array(
            1 => 'APPROVED', 
            0 => 'AWAITING APPROVAL', 
            2 => 'IMPROVE'
            ), 
        'label' => false, 
        'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified]', 
        'class' => 'TwitterVerified1', 
        'id' => $key['Tweet']['id'],
        $disabled,
        'default' => $key['Tweet']['verified']));?>

        <? if ($key['Tweet']['verified'] == 1 || $key['Tweet']['verified'] == 2) {?>
        <i><small>-<? echo $key['Tweet']['verified_by'];?></small></i>
        <?}?>
      </div>
    <div class="row">
      <div class='scheduled' id='time<?php echo $key['Tweet']['id']?>'> 
        SCHEDULE
        <hr style="margin: 5px 0;">
        <div class='notediting'>
            <?php 
            if($key['Tweet']['time'] && $key['Tweet']['published'] == 1) {
                echo date('d.m.Y', $key['Tweet']['timestamp']) . '<small>[Published]</small>' . '<br />';
            } elseif ($key['Tweet']['time']) {
                echo date('d.m.Y', $key['Tweet']['timestamp']) . '<br />';
            } else {
                    echo '';
            } 

            echo '<b class="' .date('l', $key['Tweet']['timestamp']) . '">' . strtoupper(date('l', $key['Tweet']['timestamp'])) . '</b>' . '<br />';

            echo date('H:i', $key['Tweet']['timestamp']);?>
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

            <hr style="margin: 5px 0;">

            <span class='writer' style='float: left'>WRITER <br /> <b><? echo $key['Tweet']['first_name']; ?></b></span>
      </div>
      <div class='nopadding' id=<?php echo $key['Tweet']['id'];?>>
        <?php echo $this->Form->textarea('body', array(
            'class' => 'editing', 
            'value' => $key['Tweet']['body'], 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][body]', 
            'label' => false, 
            'maxlength' => '140')); ?> 
            
            <div class="tweetButtons">
            <? if ($key['Tweet']['comments']) {
                $val = 'Comments(1)';
            } else {
                $val = 'Comments(0)';
            }?>
            <div class="empty comments" id="<? echo $key['Tweet']['id']; ?>" style="background-image: url('../img/comment1.png')">COMMENTS</div>
            <span class='savetweet'>SAVE</span>
            <span class='deletetweet' id="<? echo $key['Tweet']['id'];?>">DELETE</span>
            <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][img_url1]', 'label' => false)); ?>
            <? echo $this->Form->button('SHORTEN URLs', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
            <div id="<?echo $key['Tweet']['id'];?>-comments" style="display: none" class="empty"><? echo $this->Form->input('comments', array('value' => $key['Tweet']['comments'], 'label' => false, 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][comments]'));?></div>
            <? if ($key['Tweet']['img_url']) { ?>
                    <div class='imagecontainer'>
                    <? echo $this->Html->image($key['Tweet']['img_url'], array('style' => 'max-width:500px')); ?>
                    <? echo $this->Html->link("<div class='deleteimage'>Delete image</div>", array('action' => 'deleteImage', $key['Tweet']['id']), array('escape' => false));?>
                    </div>
            <?  }  ?>
            </div>
      </div>
      <?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $key['Tweet']['id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][id]'));
            echo $this->Form->input('verfied_by', array(
            'type' => 'hidden', 
            'value' => $this->Session->read('Auth.User.first_name'), 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][verified_by]', 
            'class' => 'verifiedby', 
            'id' => $key['Tweet']['id'] . '_' . $this->Session->read('Auth.User.first_name')));
            echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $key['Tweet']['user_id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][user_id]'));
            echo $this->Form->input('account_id', array('type' => 'hidden', 'value' => $key['Tweet']['account_id'], 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][account_id]'));?>
    </div>
</div>
    <?php } ?>

<?php echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save')); ?>

<div id='paginatorcontainer'>
<?echo $this->Paginator->numbers();?>
</div>

<script>
$('.editing').charCount({css: 'counter counter1'});
</script>