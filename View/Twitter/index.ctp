<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
<? echo $this->Html->script('charCount');
echo $this->Html->script('jquery.infinitescroll'); ?>
<?php
echo $this->Session->flash('auth');
?>
<?
echo $this->Form->create('filterAccount');
echo $this->Html->image('twitter19px.png', array('class' => 'selectimage'));
echo $this->Form->input('account', array(
    'label' => false,
    'onchange' => 'this.form.submit()',
    'options' => array('empty' => 'Select by Twitter Account', array_combine($dropdownaccounts,$dropdownaccounts)),
    'selected' => $this->Session->read('filterAccount')));
echo $this->Form->end();

echo $this->Html->image('user_purple.png', array('class' => 'selectimage'));
echo $this->Form->create('filterUser');
echo $this->Form->input('user', array(
    'label' => false,
    'onchange' => 'this.form.submit()',
    'options' => array('empty' => 'Select by User', $dropdownusers),
    'selected' => $this->Session->read('filterUser')));
echo $this->Form->end();
?>

<h2>Manage Your Team's Tweets</h2>

<div id="progress">
    <h2>PROGRESS</h2>
    <table>
        <tr class='progress firstline'>
            <td class='queuedCount'><? echo $queuedCount;?></td>
            <td class='awaitingProofCount mid'><? echo $awaitingProofCount;?></td>
            <td class='needImprovingCount'><? echo $needImprovingCount;?></td>
        </tr>
        <tr class='progress secondline'>
            <td>QUEUED</td>
            <td class='mid'>AWAITING PROOF</td>
            <td>NEED IMPROVING</td>
        </tr>
    </table>
</div>

<div id="filterLinks">
<? echo $this->Html->link('Awaiting Proof', '/twitter/index/', array('class' => 'awaitingProof'));
echo $this->Html->link('Queued', '/twitter/index/h:queued', array('class' => 'queued'));
echo $this->Html->link('Published', '/twitter/index/h:published', array('class' => 'published'));
echo $this->Html->link('Need Improving', '/twitter/index/h:improving', array('class' => 'needImproving'));
?>
</div>

<hr>

<table id="table">
<tr><td style="border: none"><?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit'));?>
<table id="refresh">
<thead class="mainheader">
    <th class='sort'><? echo $this->Paginator->sort('timestamp', 'Scheduled');?></th>
    <th class='sort'><? echo $this->Paginator->sort('screen_name', 'Account');?></th>
    <th class='sort'><? echo $this->Paginator->sort('first_name', 'Written By');?></th>
    <th class='sort'><? echo $this->Paginator->sort('body', 'Tweet');?></th>
    <th>Verified</th>
</thead>
    <?php foreach ($tweets as $key) { ?>
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

            if ($this->Session->read('Auth.User.group_id') == 2 || $params == 'h:archived') {
                $disabled = 'disabled';
            } else {
                $disabled = '';
            }?>
    <tr class="row">
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
      <td class='nopadding' id=<?php echo $key['Tweet']['id'];?>>
        <?php echo $this->Form->textarea('body', array(
            'class' => 'editing', 
            'value' => $key['Tweet']['body'], 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][body]', 
            'label' => false, 
            'maxlength' => '140')); ?> 
            
            <div class="tweetButtons">
            <? echo $this->Form->button('Shorten URLs', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
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

        <? if ($key['Tweet']['verified'] == 1 || $key['Tweet']['verified'] == 2) {?>
        <i><small>-<? echo $key['Tweet']['verified_by'];?></small></i>
        <?}?>
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
</table>
<?php echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php echo $this->Html->link('Logout', '/users/logout');?>
<?php echo $this->Paginator->next();?>

<script>
$(document).ready(function() { 
        $(".TwitterVerified1:checked").each( function() {
            if ($(this).attr('value') == 0) {
                color = '#ffcc00';
            } else if ($(this).attr('value') == 1) {
                color = '#21a750';
            } else if ($(this).attr('value') == 2) {
                color = '#ff0000';
            }
            $(this).closest( "tr" ).find('#TweetBody').css("border", "1px solid" + color);
        });

        $(".verifiedby").prop('disabled', true);

        $("#table").on("click", ".delete", function() {
            id = $(this).attr('id');
            $.ajax({url: "/twitter/delete/" + id, success: function() {
            window.location.reload(true);}});
        });

        $("#table").on("change", ".TwitterVerified1", function() {
            $("#table").css('opacity', '.4');
                if (this.checked == true) {
                    id = $(this).attr('id');
                    id = id.slice(0, -1);
                    $("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);

                    if ($(this).attr('value') == 0) {
                        color = '#ffcc00';
                    } else if ($(this).attr('value') == 1) {
                        color = '#21a750';
                    } else if ($(this).attr('value') == 2) {
                        color = '#ff0000';
                    }
                    $(this).closest( "tr" ).find('#TweetBody').css("border", "1px solid" + color);
                }
                $('#edit').ajaxSubmit();
                setTimeout(refresh, 100);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                    $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function() {
                    $("#table").css('opacity', '1');
                });
                };
        });

        $('.editing').charCount({css: 'counter counter1'});

        warnMessage = "You have unsaved changes on this page, if you leave your changes will be lost.";
        $(".editing").on('change', function () {
            window.onbeforeunload = function () {
                if (warnMessage != null) return warnMessage;
            }
        });

        $('input:submit, button:submit').on('click', function() {
            warnMessage = null;
        });

        /*$("#refresh").infinitescroll({
            navSelector  : '.next',    // selector for the paged navigation
            nextSelector : '.next a',  // selector for the NEXT link (to page 2)
            itemSelector : '.row',     // selector for all items you'll retrieve
        }, function() {
            $('.editing').charCount({css: 'counter counter1'});
        });*/
});
</script>