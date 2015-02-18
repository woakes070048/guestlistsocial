<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
<? 
echo $this->Html->script('jquery-ui-1.10.3.custom');
echo $this->Html->script('jquery-ui-timepicker-addon');
echo $this->Html->script('charCount');
echo $this->Html->script('jquery.urlshortener');
echo $this->Html->script('jquery.infinitescroll');
echo $this->Html->script('jquery.qtip.min');
echo $this->Html->css('jquery.qtip.min');
echo $this->Html->css('calendar'); ?>
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

if ($this->Session->read('access_token.account_id')) {
    echo $this->Html->image('calendar.png', array('url' => '/twitter/calendar/0', 'title' => 'Editorial Calendar', 'style' => 'margin: 10px 50px 10px 10px'));
}

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
<? echo $this->Html->link('Awaiting Proof', '/twitter/index/', array('class' => (empty($this->params['named']['h']))?'awaitingProof active' :'awaitingProof inactive'));
echo $this->Html->link('Queued', '/twitter/index/h:queued', array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='queued') )?'queued active' :'queued inactive'));
echo $this->Html->link('Published', '/twitter/index/h:published', array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='published') )?'published active' :'published inactive'));
echo $this->Html->link('Need Improving', '/twitter/index/h:improving', array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='improving') )?'needImproving active' :'needImproving inactive'));
echo $this->Html->link('Day-by-Day', '/twitter/index/h:daybyday', array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='daybyday') )?'daybyday active' :'daybyday inactive'));
echo $this->Html->Link('Not Published', '/twitter/index/h:notpublished', array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='notpublished') )?'notPublished active' :'notPublished inactive'));
?>
</div>

<hr>

<? if ($params != 'h:daybyday' && $this->Session->read('filterAccount')) {?>
    <div id='addTweetWrapper'>
<?
//Add Tweet
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'testing'), 'id' => 'submitTweet'));

        echo $this->Form->textarea('body', array('label' => false, 'type' => 'post', 'class' => 'ttt', 'placeholder' => 'Body'));
        echo $this->Form->input('timestamp', array(
            'type' => 'text', 
            'label' => false, 
            'class' => 'schedule', 
            'id' => 'schedule',
            'placeholder' => 'Date & Time'
            ));
        echo $this->Form->end(array('id' => 'tweetsubmit', 'value' => 'AddTweet', 'label' => 'ADD A TWEET')); // add new form with hidden input fields to tweet now
?>
</div>
<?}?>

<? if ($params == 'h:daybyday') {
        echo $this->Form->button('Approve All', array('class' => 'urlSubmit1 approveAll', 'type' => 'button'));
    } ?>
<div id="table">
<?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit', 'type' => 'file'));?>
<table id="refresh">
<thead class="mainheader">
    <th class='sort'><? echo $this->Paginator->sort('timestamp', 'Scheduled');?></th>
    <th class='sort'><? echo $this->Paginator->sort('screen_name', 'Account');?></th>
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
        <span class='screenName'><? echo $key['Tweet']['screen_name']; ?></span>
      </td>
      <td class='nopadding' id=<?php echo $key['Tweet']['id'];?>>
        <?php echo $this->Form->textarea('body', array(
            'class' => 'editing', 
            'value' => $key['Tweet']['body'], 
            'name' => 'data[Tweet]['.$key['Tweet']['id'].'][body]', 
            'label' => false, 
            'maxlength' => '140')); ?> 
            
            <span style='float: left'>Written by: <? echo $key['Tweet']['first_name']; ?></span>
            <div class="tweetButtons">
            <? echo $this->Form->button('Shorten URLs', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
            <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][img_url1]', 'label' => false)); ?>
            <? echo $this->Form->button('Delete', array('type' => 'button', 'class' => 'delete', 'id' => $key['Tweet']['id'])); ?>
            <? echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'smallSaveButton'));?>
            <? if ($key['Tweet']['comments']) {
                $val = 'Comments(1)';
            } else {
                $val = 'Comments(0)';
            }?>
            <? echo $this->Form->button($val, array('type' => 'button', 'class' => 'comments' , 'id' => $key['Tweet']['id'])); ?>
            <div id="<?echo $key['Tweet']['id'];?>-comments" style="display: none" class="empty"><? echo $this->Form->input('comments', array('value' => $key['Tweet']['comments'], 'label' => false, 'name' => 'data[Tweet]['.$key['Tweet']['id'].'][comments]'));?></div>
            <? if ($key['Tweet']['img_url']) { ?>
                    <div class='imagecontainer'>
                    <? echo $this->Html->image($key['Tweet']['img_url'], array('style' => 'max-width:500px')); ?>
                    <? echo $this->Html->link("<div class='deleteimage'>Delete image</div>", array('action' => 'deleteImage', $key['Tweet']['id']), array('escape' => false));?>
                    </div>
            <?  }  ?>
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
        $disabled,
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

<?php echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save')); ?>
<div id='paginatorcontainer'>
<?echo $this->Paginator->numbers();?>
</div>
</div>
<?php //echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php //echo $this->Html->link('Logout', '/users/logout');?>
<?php //echo $this->Paginator->next();?>

<script>
$(document).ready(function() { 
        <? if ($params == 'h:daybyday' && $this->Session->read('access_token.account_id')) {?>
        $('#table').css('opacity', '.4');
        $('#loading').show();
        $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function () {
            $('#table').css('opacity', '1');
            $('#loading').hide();
        });
        <? } ?>

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

        <? if ($params == 'h:queued') {?>
            $(".verifiedby").prop('disabled', false);
        <? } else { ?>
            $(".verifiedby").prop('disabled', true);
        <? } ?>

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

                <? if ($params == 'h:daybyday') {  ?>
                        $('#submitTweets').ajaxSubmit({success: function() {
                            refresh();
                        }});
                <? } else {  ?>
                        $('#edit').ajaxSubmit({success: function() {
                            refresh();
                        }});
                <? }  ?>
                //setTimeout(refresh, 500);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                    <? if ($params == 'h:daybyday') {  ?>
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                $("#table").css('opacity', '1');
                            });
                    <? } else {  ?>
                            $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function() {
                                $("#table").css('opacity', '1');
                            });
                    <? }  ?>
                    
                };

                $('#progress table').load('/twitter/progressrefresh');
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

        jQuery.urlShortener.settings.apiKey = 'AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08';
        $(".shortsingle").click(function () {
            regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
            textbox = $(this).closest('.nopadding').children('.editing');
            var longUrlLink = textbox.val().match(regex);
                jQuery.urlShortener({
                    longUrl: longUrlLink,
                    success: function (shortUrl) {
                        textbox.val(textbox.val().replace(longUrlLink, shortUrl));
                    },
                    error: function(err) {
                        $("#shortUrlInfo").html(JSON.stringify(err));
                    }
                });
        });

        $('.input.file input').on('change', function() {
            $(this).parent().css('background', "url(/img/upload_image_green.png) left center no-repeat");
        });

        $(".approveAll").click(function () {
            $(".verified").each(function () {
                if ($(this).find(".input.radio input:radio[value=0]").prop('checked') || $(this).find(".input.radio input:radio[value=2]").prop('checked')) {
                    $(this).find(".input.radio input:radio[value=1]").prop('checked', true);
                }
                $("#table").css('opacity', '.4');
                    id = $(this).find(".input.radio input:radio[value=1]").attr('id');
                    id = id.slice(0, -1);
                    $("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);

                    
            });
            $('#submitTweets').ajaxSubmit({success: function() {
                refresh();
            }});

            function refresh() {
                $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                    $("#table").css('opacity', '1');
                });
            };
            $('#progress table').load('/twitter/progressrefresh');
        });

        $("#table").on("click", ".smallSaveButton", function() {
            $("#table").css('opacity', '.4');
            <? if ($params == 'h:daybyday') {  ?>
                        $('#submitTweets').ajaxSubmit({success: function() {
                            refresh();
                        }});
                <? } else {  ?>
                        $('.qtip-content .empty').each(function () {
                            val = $(this).find('input').val();
                            id = $(this).attr('id');
                            id = id.slice(0, -9);
                            $('td#' + id).find('.tweetButtons').find('#' + id + '-comments').find('input').attr('value', val);
                        });
                        $('#edit').ajaxSubmit({success: function() {
                            refresh();
                        }});
                <? }  ?>
                //setTimeout(refresh, 500);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                    <? if ($params == 'h:daybyday') {  ?>
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                $("#table").css('opacity', '1');
                            });
                    <? } else {  ?>
                            $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function() {
                                $("#table").css('opacity', '1');
                            });
                    <? }  ?>
                    
                };

                $('#progress table').load('/twitter/progressrefresh');
        });

        $('.schedule').each(function(){
            $(this).datetimepicker({
                dateFormat: 'dd-mm-yy',
                altFormat: '@',
            });
        });
       
        //$('.comments').hover( function() {
            //id = $(this).attr('id');
            $('.comments').qtip({ 
            content: {
                text: function() {
                    id = $(this).attr('id'); 
                    return $('#' + id + '-comments').clone();
                }, 
                button: true
            },
            hide: {
                event: false
            },
            position: {
                my: 'bottom center',
                at: 'top center', 
                target: $('.comments')
            }
        });
        //})

        /*$("#refresh").infinitescroll({
            navSelector  : '.next',    // selector for the paged navigation
            nextSelector : '.next a',  // selector for the NEXT link (to page 2)
            itemSelector : '.row',     // selector for all items you'll retrieve
        }, function() {
            $('.editing').charCount({css: 'counter counter1'});
        });*/
});
</script>