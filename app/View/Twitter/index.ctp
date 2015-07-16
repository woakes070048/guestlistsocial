<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
<script src="//js.pusher.com/2.2/pusher.min.js"></script>
<? 
echo $this->Html->script('jquery-ui-1.10.3.custom');
echo $this->Html->script('jquery-ui-timepicker-addon');
echo $this->Html->script('charCount');
echo $this->Html->script('jquery.urlshortener');
echo $this->Html->script('jquery.infinitescroll');
echo $this->Html->script('jquery.qtip.min');
echo $this->Html->script('jquery.selectric.min');
echo $this->Html->script('jquery.timeago');
echo $this->Html->css('jquery.qtip.min');
echo $this->Html->css('calendar'); ?>
<?php
echo $this->Session->flash('auth');
?>
<!--<?
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
<? echo $this->Html->link('Awaiting Proof', array('controller'=>'twitter','action'=>'index'), array('class' => (empty($this->params['named']['h']))?'awaitingProof active' :'awaitingProof inactive'));
echo $this->Html->link('Queued', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'queued')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='queued') )?'queued active' :'queued inactive'));
echo $this->Html->link('Published', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'published')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='published') )?'published active' :'published inactive'));
echo $this->Html->link('Need Improving', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'improving')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='improving') )?'needImproving active' :'needImproving inactive'));
echo $this->Html->link('Day-by-Day', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'daybyday')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='daybyday') )?'daybyday active' :'daybyday inactive'));
echo $this->Html->Link('Not Published', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'notpublished')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='notpublished') )?'notPublished active' :'notPublished inactive'));
?>
</div>
-->

<div class='filter'>
    <?
    echo $this->Form->create('filter');
    echo $this->Form->input('account', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array('' => 'Select by Twitter Account', array_combine($dropdownaccounts,$dropdownaccounts)),
        'selected' => $account,
        'class' => 'filterAccount'));
    ?>

    <? if ($this->Session->read('access_token.account_id')) {
        echo $this->Html->image('calendar.png', array('url' => '/twitter/calendar/0', 'title' => 'Editorial Calendar', 'style' => 'margin: 10px 50px 10px 10px'));
    }

    echo $this->Form->input('user', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array('' => 'Select by User', $dropdownusers),
        'selected' => $user,
        'class' => 'filterUser'));

    echo $this->Form->input('status', array(
        'onchange' => 'this.form.submit()',
        'label' => false,
        'options' => array(
            '' => 'Select by Status',
            'All Statuses' => 'All Statuses',
            'queued' => 'Queued',
            'awaitingproof' => 'Awaiting Proof',
            'improving' => 'Need Improving',
            'published' => 'Published',
            'notpublished' => 'Not Published'),
        'selected' => $status,
        'class' => 'filterStatus'));

    echo $this->Form->input('team', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array(
            '' => 'Select by Team',
            $myteam),
        'selected' => $team,
        'class' => 'filterTeam'));
    echo $this->Form->end();
    ?>
</div>

<hr>

<div id='addtweetprogress'>

<? if ($params != 'h:daybyday' && $account) {?>
    <div id='addTweetWrapper'>
<?
//Add Tweet
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'testing'), 'id' => 'submitTweet'));
        echo $this->Form->input('timestamp', array(
            'type' => 'text', 
            'label' => false, 
            'class' => 'schedule', 
            'id' => 'schedule',
            'placeholder' => 'Date & Time'
            ));
        echo $this->Form->textarea('body', array('label' => false, 'type' => 'post', 'class' => 'ttt', 'placeholder' => 'Body'));
        echo $this->Form->end(array('id' => 'tweetsubmit1', 'value' => 'AddTweet', 'label' => 'ADD A TWEET')); // add new form with hidden input fields to tweet now
?>
</div>
<?}?>

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

<? if ($params == 'h:daybyday') {
    $text = 'ACTIVE';
    $link = '/';
} else {
    $text = 'NOT ACTIVE';
    $link = '/tweets?h=daybyday';
} ?>
<a href=<?echo $link;?>>
<div id='dbdbox'>
Day By Day <br /> View: <br />
<div class='calendarlarge'><b>
<?
echo $text;
?>
</b></div>
</div>
</a>

</div>

<div id="table">
<?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit', 'type' => 'file'));?>
    <?php foreach ($tweets as $key) { ?>
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
            <? $val = count($key['Comment']);
            if ($val > 9) {
                $val = '9plus';
            }?>
            <div class="empty comments" id="<? echo $key['Tweet']['id']; ?>" style="background-image: url('../img/comment<?echo $val;?>.png')">COMMENTS</div>
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
            echo $this->Form->input('verified_by', array(
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

<?php echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save', 'style' => 'margin-top:10px;')); ?>
<div id='paginatorcontainer'>
<?echo $this->Paginator->numbers();?>
</div>
</div>

<div id='noaccount'>
Please select an account from above to see the day-by-day view
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
            $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
        });
        <? } elseif ($params == 'h:daybyday' && !$this->Session->read('access_token.account_id')) {?>
        $('#table').hide();
        $('#noaccount').show();
        <?}?>

        $('.editing').charCount({css: 'counter counter1'});

        $(".TwitterVerified1").each( function() {
            if ($(this).val() == 0) {
                color = '#ffcc00';
            } else if ($(this).val() == 1) {
                color = '#21a750';
            } else if ($(this).val() == 2) {
                color = '#ff0000';
            }
            $(this).closest("#refresh").find('#TweetBody').css("border", "1px solid" + color);
            $(this).closest("#refresh").find('#TweetBody').css("border-bottom", "none");
            $(this).closest("#refresh").find('.counter1').css("border", "1px solid" + color);
            $(this).closest("#refresh").find('.counter1').css("border-top", "none");
        });

        <? if ($params == 'h:queued') {?>
            $(".verifiedby").prop('disabled', false);
        <? } else { ?>
            $(".verifiedby").prop('disabled', true);
        <? } ?>

        $("#table").on("click", ".deletetweet", function() {
            id = $(this).attr('id');
            $.ajax({url: "/twitter/delete/" + id, success: function() {
            window.location.reload(true);}});
        });

        $("#table").on("change", ".TwitterVerified1", function() {
            <? if ($params != 'h:daybyday') {?>
                $("#table").css('opacity', '.4');
                $('#edit').ajaxSubmit({success: function() {
                    refresh();
                }});
                //setTimeout(refresh, 500);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                    $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function() {
                        $("#table").css('opacity', '1');
                    });
                };

                $('#progress table').load('/twitter/progressrefresh');
                <? } else {?>
                    $(this).closest("tr").find('input[name=tosubmit]').val(true);
                    $("#table").css('opacity', '.4');
                    $('#loading').show();
                    var dat = new FormData();
                    $('input[name=tosubmit][value=true]').each(function () {
                        //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                        $(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
                            if ($(this).attr('type') == 'file') {
                                dat.append($(this).attr('name'), this.files[0]);
                            } else {
                                dat.append($(this).attr('name'), $(this).val());
                            }
                        });
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: "/editorial_calendars/editcalendartweet1",
                        data: dat,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                            });
                        }
                    });
                    $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
                <?} ?>
        });

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
                    /*id = $(this).find(".input.radio input:radio[value=1]").attr('id');
                    id = id.slice(0, -1);
                    $("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);*/

                    
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

        $("#table").on("click", ".savetweet", function() {
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
                text: function(event, api) {
                    id = $(this).attr('id'); 
                    //return $('#' + id + '-comments').clone();
                    $.ajax({
                        url: '/comments/commentrefresh/' + id
                    })
                    .then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                    }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to the status and error value
                    api.set('content.text', status + ': ' + error);
                    });

                    return 'Loading...'; // Set some initial text
                }, 
                button: true
            },
            hide: {
                event: false
            },
            position: {
                my: 'bottom center',
                at: 'top center', 
                target: 'event'
            }
        });
        //})

        $('select').selectric({
            optionsItemBuilder: function(itemData, element, index) {
                return element.val().length ? '<span class="ico ico-' + element.val() +  '"></span>' + itemData.text : itemData.text;
            }
        });

        $('.selectric .label:contains("AWAITING APPROVAL")').css({'background': 'url("../img/radioamber.png") no-repeat left center', 'padding-left': '18px', 'margin-left': '5px'});
        $('.selectric .label:contains("APPROVED")').css({'background': 'url("../img/radiogreen.png") no-repeat left center', 'padding-left': '18px', 'margin-left': '5px'});
        $('.selectric .label:contains("IMPROVE")').css({'background': 'url("../img/radiored.png") no-repeat left center', 'padding-left': '18px', 'margin-left': '5px'});

        $('.fr div').hover(function() {
            $('#userlogout').toggle();
        });

        $('#notificationbox').load('/notifications/notificationrefresh/' + <? echo $this->Session->read('Auth.User.id'); ?>);

        $('.fr img').click(function () {
            $('#notificationbox, .notificationarrow').toggle();
            if ($('#notificationbox').css('display') == 'none') {
                str = $("#notificationFrontImage").attr('src');
                str1 = str.substr(17);
                if (str1 != "9plus.png") {
                    str1 =  Number(str1.split('.')[0]) - 5;
                    if (str1 < 0) {
                        str1 = 0
                    }
                }
                $("#notificationFrontImage").attr('src', '/img/notification' + str1 + '.png');
            }
        });

        $(document).click(function(e) {   
            if(e.target.id != 'notificationbox' && e.target.id != 'notificationFrontImage') {
                $("#notificationbox, .notificationarrow").hide();
                str = $("#notificationFrontImage").attr('src');
                str1 = str.substr(17);
                if (str1 != "9plus.png") {
                    str1 =  Number(str1.split('.')[0]) - 5;
                    if (str1 < 0) {
                        str1 = 0
                    }
                }
                $("#notificationFrontImage").attr('src', '/img/notification' + str1 + '.png');
            } 
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